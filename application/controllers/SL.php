<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SL_Controller extends CI_Controller
{
    protected $model;
    protected $user_model = 'User';
    protected $use_file_upload = false;
    protected $use_image_upload = false;
    protected $return_data;
    protected $show_first_category = true;
    protected $script = false;
    protected $edit_script = false;
    protected $format = 'html';
    protected $category_id_name = 'category_id';

    // 분류기능 사용할 경우
    protected $category_model = null;

    // 날짜데이터(달력기능) 사용할 경우
    protected $use_calendar = false;
    protected $today;
    protected $now;
    protected $date;
    protected $min_date = '1900-01-01';
    protected $max_date = '2050-12-31';
    protected $timezone;

    // 페이징 관련
    protected $use_page = true;
    protected $per_page = 10;
    protected $page = 0;
    protected $no_set_page = false;

    protected $default_view_directory;
    protected $default_view_file;

    // index에서 상세정보 사용할 경우
    protected $use_index_content = false;

    // check_permission
    protected $check_permission = true;

    // assets version
    protected $assets_version = 7;
    protected $file_server_type = 'local';

    public function __construct()
    {
        parent::__construct();

        if ($this->input->is_cli_request()) {
            return true;
        }

        // 기본 헬퍼 로드
        $this->load_default_helper();

        $this->load->library('session');
        $this->load->library('layout');

        if ($this->session->userdata('branch_id')) {
            $this->load->model('Branch');
            $branch = $this->Branch->get_content($this->session->userdata('branch_id'));

            $common_data['branch'] = $branch;
        }

        if ($this->input->get_post('format') == 'json' or $this->input->get_post('json')) {
            $this->format = 'json';
        }

        $this->set_locale();
        $this->login_check();

        // ACL 로드
        $this->load->model('Acl');
        $this->permission_check();
        
        if (!empty($this->model)) {
            $common_data['model'] = ucfirst($this->model);
            $common_data['title'] = _($common_data['model']);
        }

        if ($this->input->get('popup')) {
            $this->layout->layout = 'popup';
        }

        $this->timezone = new DateTimeZone($this->config->item('time_reference'));
        $date_time_obj = new DateTime('now', $this->timezone);
        $this->today = $date_time_obj->format('Y-m-d');
        $this->now = $date_time_obj->format('Y-m-d H:i:s');
        $this->date = $this->today;
        $search_data = ['current_year' => $date_time_obj->format('Y'), 'current_month' => $date_time_obj->format('m'), 'today' => $this->today, 'timezone' => $this->timezone, 'min_date' => $this->min_date, 'max_date' => $this->max_date, 'now' => $this->now];

        if ($this->input->get('date')) {
            $this->date = $this->input->get('date');
        }

        if ($this->use_calendar) {
            if ($this->input->get('date')) {
                $this->date = $this->input->get('date');
            }
            $search_data['date'] = $this->date;
        } else {
            $search_data['date'] = $this->date;
        }

        $this->load->model('Facility');
        $locker_list = $this->Facility->get_index(1, 0);

        if ($locker_list['total']) {
            $common_data['facility_menu_id'] = $locker_list['list'][0]['id'];
        }

        $branch_list=array('total'=>0);

        if ($this->Acl->has_permission('branch_changes') or $this->session->userdata('role_id') < 4) {
                $this->load->model('AdminMoveAvailableBranch');
                $this->AdminMoveAvailableBranch->admin_id=$this->session->userdata('admin_id');
                $branch_count=$this->AdminMoveAvailableBranch->get_count();
                if(!empty($branch_count)) {
                    $branch_list=$this->AdminMoveAvailableBranch->get_index();
                } else {
                    $this->load->model('Branch');

                    if ($this->session->userdata('center_id')) {
                        if ($this->session->userdata('role_id') != 1) {
                            $this->Branch->center_id = $this->session->userdata('center_id');
                        }
                    }

                    if (!empty($branch['id'])) {
                        $this->Branch->center_id = $branch['center_id'];
                    }
                    $this->Branch->enable = true;
                    $branch_list = $this->Branch->get_index(1000, 0);
                }
        }

        $common_data['branch_list'] = $branch_list;

        if (empty($this->default_view_directory)) {
            $this->default_view_directory = $this->router->fetch_class();
        }

        if (empty($this->default_view_file)) {
            $this->default_view_file = $this->router->fetch_method();
        }

        $this->load_cdn_setting();

        if ($this->format == 'html') {
            if (ENVIRONMENT != 'production') {
                $this->assets_version = uniqid();
            }

            $this->render_default_resource();
            $this->layout->title_for_layout = _('Main Title');
        }

        $common_data['assets_version'] = $this->assets_version;
        $this->return_data = ['common_data' => $common_data, 'search_data' => $search_data];
    }


    protected function get_branch_ids()
    {
        $branch_ids=array();

        $branch_lists=$this->return_data['common_data']['branch_list'];

        if(empty($branch_lists['total'])) {
            return $branch_ids;
        }

        if(!in_array($this->session->userdata('admin_uid'),array('humake01','humake02','humake03'))) {
             return array();
        }

        foreach($branch_lists['list'] as $value) {
            $branch_ids[]=$value['id'];
        }

        return $branch_ids;
    }

    protected function load_cdn_setting()
    {
        $env_file_path = realpath(BASEPATH . DIRECTORY_SEPARATOR . '..');

        if (!file_exists($env_file_path . DIRECTORY_SEPARATOR . '.env')) {
            return false;
        }

        $dotenv = Dotenv\Dotenv::createImmutable($env_file_path);
        $dotenv->load();

        if (!empty($_ENV['FOG_PROVIDER'])) {
            if ($_ENV['FOG_PROVIDER'] == 'AWS') {
                $this->s3_options = [
                    'region' => 'ap-northeast-2',
                    'version' => 'latest',
                    'credentials' => ['key' => $_ENV['AWS_ACCESS_KEY_ID'], 'secret' => $_ENV['AWS_SECRET_ACCESS_KEY']],
                ];
            }
            $this->file_server_type = $_ENV['FOG_PROVIDER'];
        }
    }

    protected function login_check()
    {
        if ($this->format == 'html') {
            if (empty($this->session->userdata('admin_id'))) {
                redirect('/login');
            }
        } else {
            if (empty($this->session->userdata('admin_id'))) {
                echo json_encode(['result' => 'fail', 'message' => 'login first']);
                exit;
            }
        }

        return true;
    }

    protected function permission_check()
    {
        if (empty($this->check_permission)) {
            return true;
        }

        if (isset($this->permission_controller)) {
            $permission_controller = $this->permission_controller;
        } else {
            if (isset($this->parent_model)) {
                $permission_controller = decamelize($this->parent_model . 's');
            } else {
                $permission_controller = get_class($this);
            }
        }

        if (!$this->Acl->has_permission(strtolower($permission_controller))) {
            show_error('You do not have access to this section');
            exit;
        }
    }

    protected function load_default_helper()
    {
        $this->load->helper('sl');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('inflector');
    }

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        if ($this->category_model) {
            $category = $this->get_category($category_id);

            if ($category['total']) {
                $this->{$this->model}->category_id = $category['current_id'];
            }
        }

        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        if ($this->use_index_content) {
            $this->return_data['data']['content'] = $this->get_list_view_data($list);
        }

        if (isset($category)) {
            $this->return_data['data']['category'] = $category;
        }

        $this->setting_pagination(['total_rows' => $list['total']]);
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    public function index()
    {
        $this->index_data($this->input->get($this->category_id_name));
        $this->render_format();
    }

    protected function get_category($category_id = null)
    {
        if ($this->category_model) {
            $this->load->model($this->category_model);
            $category = $this->{$this->category_model}->get_index(100, 0, 'id', true, false);

            if (empty($category_id)) {
                if ($category['total']) {
                    $category['content'] = $category['list'][0];
                }
            } else {
                if (!$this->{$this->category_model}->get_count($this->input->get($this->category_id_name))) {
                    show_404();
                }

                $category['content'] = $this->{$this->category_model}->get_content($category_id);
            }

            if ($category['total']) {
                $category['current_id'] = $category['content']['id'];
            }

            return $category;
        } else {
            return ['total' => 0];
        }
    }

    protected function render_format(?array $json_array = null, $format = 'html')
    {
        if ($this->input->get('format') == 'json' or $this->input->get('json') == 'true') {
            $format = 'json';
        }

        if (empty($json_array)) {
            $json_array = $this->json_format();
        }

        if ($format == 'json') {
            echo json_encode($json_array);
        } else {
            $this->render_index_resource();
            $this->layout->render($this->default_view_directory . '/' . $this->default_view_file, $this->return_data);
        }
    }

    protected function json_format()
    {
        if (isset($this->return_data['data']['total'])) {
            if (empty($this->return_data['data']['total'])) {
                return ['result' => 'success', 'total' => $this->return_data['data']['total']];
            } else {
                return ['result' => 'success', 'total' => $this->return_data['data']['total'], 'list' => $this->return_data['data']['list']];
            }
        } else {
            return ['result' => 'error', 'message' => validation_errors()];
        }
    }

    protected function render_view_format()
    {
        if ($this->input->get('format') == 'json' or $this->input->get('json') == 'true') {
            echo json_encode(['result' => 'success', 'content' => $this->return_data['data']['content']]);
        } else {
            $this->render_index_resource();
            $this->layout->render($this->default_view_directory . '/' . $this->router->fetch_method(), $this->return_data);
        }
    }

    protected function render_default_resource()
    {
        if (ENVIRONMENT == 'development') {
            $this->layout->add_css('bootstrap.min.css');
            $this->layout->add_css('animate.min.css');
            $this->layout->add_css('bootstrap-datepicker.css');
            $this->layout->add_css('style.css?version=' . $this->assets_version);
            $this->layout->add_css('jquery.fancybox-1.3.4.css');
            $this->layout->add_css('font-face.css');
            $this->layout->add_css('jquery-ui.css');
            $this->layout->add_css('index.css?version=' . $this->assets_version);
        } else {
            // uglifycss --output common.min.css bootstrap.min.css animate.min.css bootstrap-datepicker.css style.css jquery.fancybox-1.3.4.css font-face-product.css jquery-ui.css index.css
            $this->layout->add_css('common.min.css?version=' . $this->assets_version);
        }

        if (ENVIRONMENT == 'development') {
            $this->layout->add_js('jquery.min.js');
            $this->layout->add_js('popper.min.js');
            $this->layout->add_js('bootstrap.min.js');
            $this->layout->add_js('jquery-ui-1.10.3.custom.min.js');
            $this->layout->add_js('jquery.form.min.js');
            $this->layout->add_js('jquery.fancybox.1.3.4.js');
            $this->layout->add_js('jquery.pagination.js');
            $this->layout->add_js('bootstrap-datepicker.min.js');
            $this->layout->add_js('moment.js');
            $this->layout->add_js('jquery.ui.monthpicker.js'); 
            $this->layout->add_js('common.js?version=' . $this->assets_version);
        } else {
            // uglifyjs --output common.min.js jquery.min.js popper.min.js bootstrap.min.js jquery-ui-1.10.3.custom.min.js jquery.form.min.js jquery.fancybox.1.3.4.js jquery.pagination.js bootstrap-datepicker.min.js moment.js jquery.ui.monthpicker.js common.js
            $this->layout->add_js('common.min.js?version=' . $this->assets_version);
        }
    }

    protected function render_index_resource()
    {
        if ($this->script) {
            $this->return_data['script'] = $this->config->item('js_file_path') . $this->script . '?version=' . $this->return_data['common_data']['assets_version'];
            $this->layout->add_js($this->script . '?version=' . $this->assets_version);
        }
    }

    protected function get_error_messages()
    {
        $message = [
            'required' => _('The %s field is required.'),
            'min_length' => _('The %s field must be at least %s characters in length.'),
            'max_length' => _('The %s field cannot exceed %s characters in length.'),
            'numeric' => _('The %s field must contain only numbers.'),
            'integer' => _('The %s field must contain only numbers.'),
            'is_unique' => _('The %s field must contain a unique value.'),
            'matches' => _('The %s field does not match the %s field.'),
            'in_list' => _('The %s field must be one of: %s.'),
            'greater_than' => _('The %s field must contain a number greater than %s.'),
            'less_than' => _('The %s field must contain a number less than %s.'),
            'valid_email' => _('The %s field must contain a valid email address.'),
            'valid_date' => _('The %s field must contain a valid date.'),
            'valid_time' => _('The %s field must contain a valid time.'),
            'valid_date_after' => _('The %s field must after date.'),
            'valid_date_before' => _('The %s field must smaller than or equal origin enddate.'),
            'valid_search_date' => _('The search end date must after search start date.'),
            'valid_time_after' => _('The %s field must after time.'),
            'not_past_date' => _('The %s field must greater than or equal today.'),
        ];

        return $message;
    }

    final public function set_message()
    {
        $message = $this->get_error_messages();

        foreach ($message as $key => $value) {
            $this->form_validation->set_message($key, $value);
        }
    }

    public function add()
    {
        $this->load->library('form_validation');
        $this->set_form_validation();
        $this->set_message();

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
                $this->set_add_form_data();
                $this->render_format();
            } else {
                echo json_encode(['result' => 'error', 'message' => validation_errors()]);
            }
        } else {
            $this->load->model($this->model);
            $data = $this->set_insert_data($this->input->post(null, true));

            if ($id = $this->{$this->model}->insert($data)) {
                $this->after_insert_data($id, $data);

                if ($this->format == 'json') {
                    echo json_encode(['result' => 'success', 'inserted_id' => $id, 'message' => $this->insert_complete_message($id), 'redirect_path' => $this->add_redirect_path($id)]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'success', 'message' => $this->insert_complete_message($id)]);
                    redirect($this->add_redirect_path($id));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(['result' => 'error', 'message' => _('Insert Fail')]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'danger', 'message' => _('Insert Fail')]);
                    redirect($this->router->fetch_class() . '/add');
                }
            }
        }
    }

    protected function set_add_form_data()
    {
    }

    protected function set_insert_data($data)
    {
        return $data;
    }

    protected function after_insert_data($id, $data)
    {
    }

    protected function add_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return str_replace('_', '-', $this->router->fetch_class()) . '/view/' . $id;
        }
    }

    public function edit($id = null)
    {
        $this->load->library('form_validation');
        $this->set_form_validation($id);
        $this->set_message();

        $before_content = $this->get_view_content_data($id);

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
                $this->set_edit_form_data($before_content);
                $this->render_format();
            } else {
                echo json_encode(['result' => 'error', 'message' => validation_errors()]);
            }
        } else {
            $data = $this->input->post(null, true);
            $data['edit_content'] = $before_content;

            $data = $this->set_update_data($id, $data);

            if ($this->{$this->model}->update($data)) {
                $this->after_update_data($id, $data);

                if ($this->format == 'json') {
                    echo json_encode(['result' => 'success', 'message' => $this->update_complete_message($id), 'redirect_path' => $this->edit_redirect_path($id)]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'success', 'message' => $this->update_complete_message($id)]);
                    redirect($this->edit_redirect_path($id));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(['result' => 'error', 'message' => _('Update Fail')]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'danger', 'message' => _('Update Fail')]);
                    redirect($this->router->fetch_class() . '/edit/' . $id);
                }
            }
        }
    }

    protected function after_update_data($id, $data)
    {
    }

    protected function set_edit_form_data(array $content)
    {
        if (!empty($content['category_id'])) {
            $category = $this->get_category($content['category_id']);
            $this->return_data['data']['category'] = $category;
        }

        $this->return_data['data']['content'] = $content;
    }

    protected function set_update_data($id, $data)
    {
        $data['id'] = $id;

        return $data;
    }

    protected function edit_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            $return_url = str_replace('_', '-', $this->router->fetch_class()) . '/view/' . $id;
            if ($this->input->get('page')) {
                $return_url .= '?page=' . $this->input->get('page');
            }

            return $return_url;
        }
    }

    protected function get_list_view_data($list)
    {
        if ($this->input->get('id')) {
            if (!$this->{$this->model}->get_count($this->input->get('id'))) {
                show_404();
            }

            $content = $this->get_view_data($this->input->get('id'));
        } else {
            if (empty($list['total'])) {
                $content = false;
            } else {
                $content = $list['list'][0];
            }
        }

        return $content;
    }

    protected function get_view_data($id)
    {
        return $this->get_view_content_data($id);
    }

    final protected function get_view_content_data($id)
    {
        $this->load->model($this->model);
        if (!$this->{$this->model}->get_count($id)) {
            if ($this->format == 'json') {
                echo json_encode(['result' => 'error', 'code' => '404', 'message' => '404 Page Not Found']);
            } else {
                show_404();
            }
            exit;
        }
        
        $content = $this->{$this->model}->get_content($id);
        if (empty($content)) {
            if ($this->format == 'json') {
                echo json_encode(['result' => 'error', 'code' => '404', 'message' => '404 Page Not Found, content']);
            } else {
                show_404();
            }
            exit;
        }

        return $content;
    }

    public function view($id = null)
    {
        if (empty($id)) {
            show_404();
        }

        if ($content = $this->get_view_data($id)) {
            $this->return_data['data'] = ['content' => $content];
        }

        $this->render_view_format();
    }

    public function delete_confirm($id)
    {
        $this->return_data['data']['id'] = $id;
        $this->layout->render('/layout/elements/delete_confirm', $this->return_data);
    }

    public function set_delete_form_validation()
    {
        $this->form_validation->set_rules('id', _('id'), 'integer');
    }

    public function delete($id)
    {
        $this->load->library('form_validation');
        $this->set_delete_form_validation();
        $this->set_message();

        $content = $this->get_view_content_data($id);

        if ($this->form_validation->run() == false) {
            $this->return_data['data']['content'] = $content;
            if ($this->format == 'html') {
                $this->delete_confirm($id);
            } else {
                echo json_encode(['result' => 'error', 'message' => validation_errors()]);
            }
        } else {
            $data = $this->set_delete_data($content, $this->input->post(null, true));

            if ($this->{$this->model}->delete($id)) {
                $this->after_delete_data($content, $data);

                if ($this->format == 'json') {
                    echo json_encode(['result' => 'success', 'message' => $this->delete_complete_message($content), 'redirect_path' => $this->delete_redirect_path($content)]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'success', 'message' => $this->delete_complete_message($content)]);
                    redirect($this->delete_redirect_path($content));
                }
            } else {
                $this->session->set_flashdata('message', ['type' => 'danger', 'message' => _('Delete Fail')]);
                if ($this->format == 'json') {
                    echo json_encode(['result' => 'error', 'message' => _('Delete Fail')]);
                } else {
                    redirect($this->delete_redirect_path($content));
                }
            }
        }
    }

    protected function set_delete_data(array $content, $data = null)
    {
        return $data;
    }

    protected function after_delete_data(array $content, $data = null)
    {
    }

    protected function delete_redirect_path(array $content)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return '/' . $this->router->fetch_class();
        }
    }

    protected function insert_complete_message($id)
    {
        return _('Successfully Created Article');
    }

    protected function update_complete_message($id)
    {
        return _('Successfully Updated Article');
    }

    protected function delete_complete_message(array $content)
    {
        return _('Successfully Deleted Article');
    }

    protected function set_page()
    {
        if ($this->no_set_page) {
            return true;
        }

        if ($this->input->get('per_page')) {
            $this->per_page = $this->input->get('per_page');
        }

        if ($this->input->get('page')) {
            $this->page = ($this->input->get('page') - 1) * $this->per_page;
        }
    }

    protected function set_form_validation($id = null)
    {
        // $this->form_validation -> set_rules('title', _('Title'), 'required|min_length[3]|max_length[60]');
    }

    public function valid_date($date)
    {
        if (empty($date)) {
            return true;
        }

        if ($date == '0000-00-00') {
            return false;
        }

        $date_a = explode('-', $date);
        if (!is_array($date_a)) {
            return false;
        }

        if (count($date_a) < 3) {
            return false;
        }

        if (checkdate($date_a[1], $date_a[2], $date_a[0])) {
            $b = new DateTime($date, $this->timezone);
            if ($b->format('Y') < 1900 or $b->format('Y') > 2100) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public function valid_time($str)
    {
        if (empty($str)) {
            return false;
        }

        //Assume $str SHOULD be entered as HH:MM

        list($hh, $mm) = explode(':', $str);

        if (!is_numeric($hh) || !is_numeric($mm)) {
            return false;
        } elseif ((int)$hh > 24 || (int)$mm > 59) {
            return false;
        } elseif (mktime((int)$hh, (int)$mm) === false) {
            return false;
        }

        return true;
    }

    public function valid_date_after($after_date, $before_date)
    {
        return $this->valid_time_after($after_date, $before_date);
    }

    public function valid_time_after($after_time, $before_time)
    {
        $beforeObj = new DateTime($before_time, $this->timezone);
        $afterObj = new DateTime($after_time, $this->timezone);
        $diff = $beforeObj->diff($afterObj);
        if ($diff->format('%R') == '+') {
            return true;
        }

        return false;
    }

    public function valid_date_before($before_date, $after_date = null)
    {
        if (empty($after_date)) {
            $after_dateObj = new DateTime($this->today, $this->timezone);
        } else {
            $after_dateObj = new DateTime($after_date, $this->timezone);
        }

        if (new DateTime($before_date, $this->timezone) > $after_dateObj) {
            return false;
        }

        return true;
    }

    public function not_past_date($date)
    {
        $date_obj = new DateTime($date, $this->timezone);
        $today_obj = new DateTime($this->today, $this->timezone);

        $date_diff = $today_obj->diff($date_obj);
        if ($date_diff->format('%R') == '+') {
            return true;
        }

        return false;
    }

    protected function get_user_list($per_page = 100, $page = 0, $search = true)
    {
        $this->load->model($this->user_model);
        
        if($this->session->userdata('center_id')) {
            if($this->input->get('branch_id')) {
                $this->{$this->user_model}->branch_id=$this->input->get('branch_id');
            }
         }

        if ($this->input->get('trainer')) {
            $this->{$this->user_model}->trainer_id = $this->input->get('trainer');
        }

        if ($this->input->get('course')) {
            $this->{$this->user_model}->course_id = $this->input->get('course');
            if ($this->input->get('quantity_only')) {
                $this->{$this->user_model}->quantity_only = $this->input->get('quantity_only');
            }
        }

        if($this->input->get('message_type')) {
            $this->{$this->user_model}->phone_only = false;
            $this->{$this->user_model}->push = false;

            switch($this->input->get('message_type')) {
                case 'push' :
                    $this->{$this->user_model}->push = true;
                    break;
                case 'wapos' :
                    $this->{$this->user_model}->wapos = true;
                    break;                    
                default :
                    $this->{$this->user_model}->phone_only = true;                                
            }
        } else {
            if ($this->input->get('phone_only')) {
                $this->{$this->user_model}->phone_only = true;
            }

            if ($this->input->get('push')) {
                $this->{$this->user_model}->push = $this->input->get('push');
            }
        }

        if ($this->input->get('group')) {
            $this->{$this->user_model}->group = $this->input->get('group');
        }

        if ($this->session->userdata('show_omu')) {
            if ($this->session->userdata('is_fc')) {
                $this->{$this->user_model}->fc_id = $this->session->userdata('admin_id');
            }

            if ($this->session->userdata('is_trainer')) {
                $this->{$this->user_model}->trainer_id = $this->session->userdata('admin_id');
            }
        }

        if ($search) {
            if ($this->input->get('search_type')) {
                $search_type = $this->input->get('search_type');

                if ($search_type == 'field') {
                    if ($this->input->get('search_field') and $this->input->get('search_word')) {
                        $search_field = $this->input->get('search_field');
                        $search_word = trim($this->input->get('search_word'));

                        if ($search_field == 'phone') {
                            $search_word = str_replace('-', '', $search_word);
                        }

                        $search_param = ['search_type' => 'field', 'search_field' => $search_field, 'search_word' => $search_word];

                        if ($this->input->get('search_field')) {
                            $this->return_data['search_data']['search_field'] = $this->input->get('search_field');
                        }

                        if ($this->input->get('search_word')) {
                            $this->return_data['search_data']['search_word'] = $this->input->get('search_word');
                        }
                    }
                }

                if (isset($search_param)) {
                    $this->{$this->user_model}->search_param = $search_param;
                }

                $this->return_data['search_data']['search_type'] = $search_type;
            }
        }

        if ($this->input->get_post('user_id')) {
            $this->{$this->user_model}->user_id = $this->input->get_post('user_id');
        }

        if ($this->input->get_post('branch_id')) {
            $this->{$this->user_model}->branch_id = $this->input->get_post('branch_id');
        }

        $this->{$this->user_model}->all = true;
        $list = $this->{$this->user_model}->get_index($per_page, $page);

        $this->return_data['data']['user'] = $list;

        return $list;
    }

    protected function get_admin_list($type = 'all', $per_page = 1000, $page = 0, $assign_return_data = true)
    {
        $this->load->model('Employee');
        $role_ids = [3, 4, 5, 6, 7];

        if (in_array($this->session->userdata('role_id'), [1, 2])) {
            $role_ids[] = 2;
        }

        $status = ['H'];
        if ($this->input->get('status')) {
            $get_status = $this->input->get('status');
            if (is_array($get_status)) {
                foreach ($get_status as $g_status) {
                    if (in_array($g_status, ['A'])) {
                        $get_status = ['H', 'R', 'L'];
                        break;
                    }

                    if (in_array($g_status, ['H', 'R', 'L'])) {
                        $get_status[] = $g_status;
                    }
                }
                $status = $get_status;
            }
        }

        if($this->session->userdata('center_id')) {
            if($this->input->get('branch_id')) {
                $this->Employee->branch_id=$this->input->get('branch_id');
            }
         }

        $this->Employee->status = $status;
        $this->Employee->role_ids = $role_ids;

        if (isset($search_word)) {
            $this->Employee->search_word = $$this->input->get('employee_name');
        }

        $this->Employee->is_fc = false;
        $this->Employee->is_trainer = false;
        switch ($type) {
            case 'fc':
                $this->Employee->is_fc = true;
                break;
            case 'trainer':
                $this->Employee->is_trainer = true;
                break;
        }
        $admin = $this->Employee->get_index($per_page, $page,'name',false);

        if ($admin['total']) {
            if ($this->input->get('employee_id')) {
                $admin['content'] = $this->Employee->get_content($this->input->get('employee_id'));
            } else {
                $admin['content'] = $admin['list'][0];
            }
        } else {
            $admin['content'] = false;
        }

        if ($assign_return_data) {
            $this->return_data['data']['admin'] = $admin;
            $this->return_data['search_data']['status'] = $status;
        }

        return $admin;
    }

    protected function set_locale()
    {
        if (!function_exists('_')) {
            echo 'gettext function not exists';
        }

        /* i18n locale */
        $lang_param = $this->input->get('locale');
        if ($lang_param) {
            switch ($lang_param) {
                case 'en':
                    $language = 'english';
                    break;
                case 'zh-CN':
                    $language = 'chinese';
                    break;
                default:
                    $language = 'korean';
            }

            if (in_array($language, ['english', 'chinese'])) {
                $this->session->set_userdata(['language' => $language]);
            } else {
                $this->session->unset_userdata('language');
            }
        } else {
            if ($this->session->userdata('language')) {
                $language = $this->session->userdata('language');
            } else {
                $language = 'korean';
            }
        }

        switch ($language) {
            case 'chinese':
                $locale = 'Zh_CN.UTF-8';
                break;
            case 'english':
                $locale = 'en_US.UTF-8';
                break;
            default:
                $locale = 'ko_KR.UTF-8';
        }

        putenv('LC_ALL=' . $locale);
        setlocale(LC_ALL, $locale);

        bindtextdomain('messages', APPPATH . DIRECTORY_SEPARATOR . 'language');
        bind_textdomain_codeset('messages', 'UTF-8');
        textdomain('messages');
    }

    protected function setting_pagination(array $config)
    {
        $this->load->library('pagination');

        if (empty($config['per_page'])) {
            $config['per_page'] = $this->per_page;
        }

        if (empty($config['base_url'])) {
            if ($this->router->fetch_method() == 'index' or $this->router->fetch_method() == 'edit') {
                $config['base_url'] = base_url() . $this->router->fetch_class();
            } else {
                $config['base_url'] = base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method();
            }
        }
        $config['page_query_string'] = true;
        $config['use_page_numbers'] = true;
        $config['query_string_segment'] = 'page';

        $query_string = $this->input->get();
        if (isset($query_string['page'])) {
            unset($query_string['page']);
        }

        if (count($query_string) > 0) {
            $config['suffix'] = '&' . http_build_query($query_string, '', '&');
            $config['first_url'] = $config['base_url'] . '?' . http_build_query($query_string, '', '&');
        }

        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = '&laquo; ' . _('First');
        $config['first_tag_open'] = '<li class="prev page-item">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = _('Last') . ' &raquo;';
        $config['last_tag_open'] = '<li class="next page-item">';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = '▶';
        $config['next_tag_open'] = '<li class="next page-item">';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = '◀';
        $config['prev_tag_open'] = '<li class="prev page-item">';
        $config['prev_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active page-item"><a href="" class="page-link">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $config['attributes'] = ['class' => 'page-link'];
        $this->pagination->initialize($config);
    }
}
