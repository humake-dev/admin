<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Validate_person.php';
require_once 'Pagination_aside.php';

class Temp_users extends SL_Controller
{
    use Validate_person;
    use Pagination_aside;

    protected $user_model = 'TempUser';
    protected $permission_controller = 'users';
    protected $model = 'TempUser';
    protected $script = 'temp-users/index.js';

    protected function index_data($category_id = null)
    {
        $this->common_index();

        if (isset($this->return_data['data']['content']['id'])) {
            $this->get_other_content($this->return_data['data']['content']['id']);
        }

        $this->return_data['data']['fc'] = $this->get_admin_list('fc');
        $this->return_data['data']['trainer'] = $this->get_admin_list('trainer');

        $this->default_view_file = 'view';
    }

    private function get_other_content($id)
    {
        $this->load->model('TempUserContent');
        $this->TempUserContent->temp_user_id = $id;
        $this->return_data['other_data']['memo'] = $this->TempUserContent->get_index(3, 0);
    }

    public function memo($id)
    {
        if ($content = $this->common_index($id)) {
            $this->load->model('TempUserContent');
            $this->TempUserContent->temp_user_id = $content['id'];
            $this->return_data['data']['memo'] = $this->TempUserContent->get_index(100, 0);
        }

        $this->render_format();
    }

    public function counsels($id)
    {
        if ($content = $this->common_index($id)) {
            $this->load->model('Counsel');
            $this->Counsel->temp_user_id = $content['id'];
            $this->return_data['data']['list'] = $this->Counsel->get_index(100, 0);
        }

        $this->render_format();
    }

    public function messages($id)
    {
        if ($content = $this->common_index($id)) {
            $this->load->model('Message');
            $this->Message->temp_user_id = $content['id'];
            $this->return_data['data']['list'] = $this->Message->get_index(100, 0);
        }

        $this->render_format();
    }

    public function view($id = null)
    {
        if (empty($id)) {
            show_404();
        }

        $this->common_index($id);

        if (isset($this->return_data['data']['content']['id'])) {
            $this->get_other_content($this->return_data['data']['content']['id']);
        }

        $this->return_data['data']['fc'] = $this->get_admin_list('fc');
        $this->return_data['data']['trainer'] = $this->get_admin_list('trainer');

        $this->render_format(array('result' => 'success', 'content' => $this->return_data['data']['content']));
    }

    private function common_index($id = null)
    {
        $this->set_page();

        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());

        $this->form_validation->set_rules('search_type', _('Search Type'), 'in_list[field,status1,status2,status3,status4,status5,status6,status7,status8]');
        $this->form_validation->set_rules('search_field', _('Search Field'), 'in_list[name,card_no,phone]');
        $this->form_validation->set_rules('search_word', _('Search Word'), 'trim');
        $this->form_validation->set_rules('term', _('Term'), 'in_list[all,month,week,day]');

        if ($this->form_validation->run() == false) {
            $list = $this->get_user_list($this->per_page, $this->page, false);
        } else {
            $list = $this->get_user_list($this->per_page, $this->page);
        }

        if ($this->input->get('search_type')) {
            $this->return_data['search_data']['search_type'] = $this->input->get('search_type');

            if ($this->input->get('search_field')) {
                $this->session->set_userdata('search_field', $this->input->get('search_field'));
            }
        }

        if (empty($id)) {
            $content = $this->get_list_view_data($list);
        } else {
            $content = $this->TempUser->get_content($id);
        }

        if ($this->router->fetch_method() == 'index') {
            $this->setting_pagination(array('base_url' => base_url() . 'temp-users', 'total_rows' => $this->return_data['data']['user']['total']));
        } else {
            $this->setting_pagination(array('base_url' => base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $id, 'total_rows' => $this->return_data['data']['user']['total']));
        }
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->return_data['data']['content'] = $content;
        $this->return_data['data']['user_content'] = $content;

        return $content;
    }

    protected function set_add_form_data($id = null)
    {
        $this->return_data['data']['fc'] = $this->get_admin_list('fc');
        $this->return_data['data']['trainer'] = $this->get_admin_list('trainer');

        $this->script = 'temp-users/add.js';
    }

    protected function set_edit_form_data(array $content)
    {
        // Insert 시의 데이터 처리와 동일
        $this->set_add_form_data();

        // 그 외 추가 항목들
        $this->set_page();

        $list = $this->get_user_list($this->per_page, $this->page);

        $this->setting_pagination(array('base_url' => base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $content['id'], 'total_rows' => $this->return_data['data']['user']['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->return_data['data']['content'] = $content;
        $this->return_data['data']['user_content'] = $content;
    }

    protected function set_insert_data($data)
    {
        $data['name'] = trim($data['name']);

        if (empty($data['phone'])) {
            $data['phone'] = null;
        } else {
            $data['phone'] = $this->create_valid_phone($data['phone']);
        }

        if (empty($data['registration_date'])) {
            $data['registration_date'] = $this->today;
        }

        if (empty($data['fc_id'])) {
            $data['fc_id'] = null;
        }

        if (empty($data['trainer_id'])) {
            $data['trainer_id'] = null;
        }

        if (empty($data['job_id'])) {
            $data['job_id'] = null;
        }

        if (empty($data['visit_route_id'])) {
            $data['visit_route_id'] = null;
        }

        // 말안되는 데이터들 제거
        if (empty($data['birthday'])) {
            $data['birthday'] = null;
        } else {
            $b = new DateTime($data['birthday'], $this->timezone);
            if ($b->format('Y') < 1900 or $b->format('Y') > 2100) {
                $data['birthday'] = null;
            }
        }

        return $data;
    }

    protected function set_update_data($id, $data)
    {
        $data = $this->set_insert_data($data);
        $data['id'] = $id;

        if ($data['birthday'] == '0000-00-00') {
            $data['birthday'] = null;
        }

        return $data;
    }

    protected function after_insert_data($id, $data)
    {
        if (empty($data['trainer_id'])) {
            if (!empty($data['edit_content'])) {
                $this->load->model('TempUserTrainer');
                $trainer_count = $this->TempUserTrainer->get_count_by_parent_id($id);

                if (!empty($trainer_count)) {
                    $this->TempUserTrainer->delete($data['edit_content']['tut_id']);
                }
            }
        } else {
            $this->load->model('TempUserTrainer');
            $trainer_count = $this->TempUserTrainer->get_count_by_parent_id($id);

            if (empty($trainer_count)) {
                $this->TempUserTrainer->insert(array('trainer_id' => $data['trainer_id'], 'temp_user_id' => $id));
            } else {
                if ($data['edit_content']['trainer_id'] != $data['trainer_id']) {
                    $this->TempUserTrainer->update(array('trainer_id' => $data['trainer_id'], 'id' => $data['edit_content']['tut_id']));
                }
            }
        }

        if (empty($data['fc_id'])) {
            if (!empty($data['edit_content'])) {
                $this->load->model('TempUserFc');
                $fc_count = $this->TempUserFc->get_count_by_parent_id($id);

                if (!empty($fc_count)) {
                    $this->TempUserFc->delete($data['edit_content']['tufc_id']);
                }
            }
        } else {
            $this->load->model('TempUserFc');
            $fc_count = $this->TempUserFc->get_count_by_parent_id($id);

            if (empty($fc_count)) {
                $this->TempUserFc->insert(array('fc_id' => $data['fc_id'], 'temp_user_id' => $id));
            } else {
                if ($data['edit_content']['fc_id'] != $data['fc_id']) {
                    $this->TempUserFc->update(array('fc_id' => $data['fc_id'], 'id' => $data['edit_content']['tufc_id']));
                }
            }
        }
    }

    protected function after_update_data($id, $data)
    {
        return $this->after_insert_data($id, $data);
    }

    public function select()
    {
        $this->set_page();

        $this->load->model($this->model);

        if ($this->input->get('search_field') and $this->input->get('search_word')) {
            $search_field = $this->input->get('search_field');
            $search_word = trim($this->input->get('search_word'));

            if ($search_field == 'phone') {
                $search_word = str_replace('-', '', $search_word);
            }

            $search_param = array('search_type' => 'field', 'search_field' => $search_field, 'search_word' => $search_word);

            if ($this->input->get('search_field')) {
                $this->return_data['search_data']['search_field'] = $this->input->get('search_field');
            }

            if ($this->input->get('search_word')) {
                $this->return_data['search_data']['search_word'] = $this->input->get('search_word');
            }

            if (isset($search_param)) {
                $this->{$this->model}->search_param = $search_param;
            }
        }

        $this->return_data['data'] = $this->{$this->model}->get_index($this->per_page, $this->page);

        $this->setting_pagination(array('total_rows' => $this->return_data['data']['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->script = 'temp-users/select.js';
        $this->render_format();
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('name', _('Name'), 'required|trim|min_length[2]|max_length[60]');
        $this->form_validation->set_rules('phone', _('Phone'), 'min_length[4]|max_length[15]');
        $this->form_validation->set_rules('gender', _('Gender'), 'in_list[1,0]');
        $this->form_validation->set_rules('registration_date', _('Registration Date'), 'callback_valid_date');
        $this->form_validation->set_rules('email', _('Email'), 'valid_email');
        $this->form_validation->set_rules('fc_id', _('User FC'), 'integer');
        $this->form_validation->set_rules('trainer_id', _('User Trainer'), 'integer');

        if ($this->input->post('birthday')) {
            $this->form_validation->set_rules('birthday', _('Birthday'), 'callback_valid_date');
        }

        $this->form_validation->set_rules('birthday_type', _('Birthday Type'), 'in_list[S,L]');
    }

    protected function after_delete_data(array $content, $data = null)
    {
        if (!empty($content['counsel_id'])) {
            $this->load->model('Counsel');
            $this->Counsel->delete($content['counsel_id']);
        }
    }
}
