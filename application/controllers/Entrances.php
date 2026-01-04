<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Pagination_aside.php';

class Entrances extends SL_Controller
{
    use Pagination_aside;

    protected $model = 'Entrance';
    protected $permission_controller = 'users';
    protected $use_calendar = true;
    protected $use_index_content = true;
    protected $check_permission = false;
    protected $script = 'entrances/index.js';
    protected $user_id;
    protected $facility_card_id;

    protected function login_check()
    {
        return true;
    }

    protected function index_data($category_id = null)
    {
        $this->common_index(null);
    }

    public function view($id = null)
    {
        if (empty($id)) {
            show_404();
        }

        $this->common_index($id);
        $this->layout->add_js($this->script . '?version=' . $this->assets_version);
        $this->render_view_format();
    }

    protected function set_add_form_data()
    {
        $this->script = 'entrances/add.js';
    }

    protected function common_index($id)
    {
        $this->set_page();

        $this->load->model($this->model);

        if ($this->input->get('user_id')) {
            $this->{$this->model}->user_id = $this->input->get('user_id');
        } else {
            $this->{$this->model}->date = $this->date;
        }

        $list = $this->{$this->model}->get_index($this->per_page, $this->page);

        $this->return_data['data'] = $list;
        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        if ($this->input->get('user_id')) {
            return true;
        }

        if (empty($id)) {
            $content = $this->get_list_view_data($list);
        } else {
            $content = $this->{$this->model}->get_content($id);
        }

        if (empty($content)) {
            $this->return_data['data']['enroll'] = array('total' => 0);
        } else {
            $user_id = $content['user_id'];

            $this->load->model('Enroll');
            $this->Enroll->user_id = $user_id;
            $this->Enroll->get_current_only = true;
            $enrolls = $this->Enroll->get_index(10, 0);

            $this->load->model('Rent');
            $this->Rent->user_id = $user_id;
            $this->Rent->get_current_only = true;
            $rents = $this->Rent->get_index(1, 0, 'start_date');

            $this->return_data['data']['enroll'] = $enrolls;

            if ($enrolls['total']) {
                $this->return_data['data']['enroll']['content'] = $enrolls['list'][0];
            }

            if ($rents['total']) {
                $this->return_data['data']['rent'] = $rents['list'][0];
            }
        }

        $this->return_data['data']['content'] = $content;
    }

    protected function set_insert_data($data)
    {
        $date = $data['date'];

        if (empty($data['time'])) {
            $time = date('H:i:s');
        } else {
            $time = $data['time'];
        }

        $data['in_time'] = $date . ' ' . $time;

        if (empty($data['user_id']) and !empty($data['card_no'])) {
            $data['user_id'] = $this->user_id;

            if (!empty($data['branch_id'])) {
                $date = new DateTime($data['date'] . $data['time'], $this->timezone);
                $data['in_time'] = $date->format('Y-m-d H:i:s');
            }
        }

        return $data;
    }

    public function send_message($id)
    {
        $data = $this->input->post(null, true);
        if (!empty($data['branch_id']) and !empty($data['card_no'])) {
            $data['user_id'] = $this->user_id;
        }

        $this->load->model('User');
        $user_content = $this->User->get_content($data['user_id']);
        $user_content['in_time'] = $this->now;

        $socket = new HemiFrame\Lib\WebSocket\WebSocket('localhost', 8080);
        $socket->on('receive', function ($client, $data) use ($socket) {
        });
        $client = $socket->connect();
        if ($client) {
            $socket->sendData($client, json_encode(array('user' => $user_content)));
            $socket->disconnectClient($client);
        }
    }

    protected function after_insert_data($id, $data)
    {
        //$this->send_message($id);

        if (!empty($this->facility_card_id)) {
            $this->load->model('EntranceCard');
            $this->EntranceCard->insert(array('parent_id' => $id, 'facility_card_id' => $this->facility_card_id));
        }
    }

    protected function add_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            $data = $this->input->post(null, true);
            if (empty($data['user_id']) and !empty($data['card_no'])) {
                $data['user_id'] = $this->user_id;
            }

            return '/home/attendances/' . $data['user_id'] . '?date=' . $this->input->post('date');
        }
    }

    protected function insert_complete_message($id)
    {
        return _('Successfully Attend');
    }

    public function valid_card_no($card_no)
    {
        $data = $this->input->post(null, true);

        if ($this->session->userdata('branch_id')) {
            $this->load->model('User');
            $content = $this->User->get_content_by_card_no($data['card_no'], $this->session->userdata('branch_id'));

            if (empty($content)) {
                $this->form_validation->set_message('valid_card_no', sprintf(_('Branch %s and Card %s Not Exists'), $this->session->userdata('branch_name'), $data['card_no']));

                return false;
            }

            $this->user_id = $content['id'];

            return true;
        } else {
            if (empty($data['branch_id'])) {
                $this->form_validation->set_message('valid_card_no', sprintf(_('The %s field is required.'), _('Branch')));

                return false;
            }

            $this->load->model('AccessController');
            $ac_content = $this->AccessController->get_content_by_branch_id($data['branch_id']);

            if (empty($ac_content)) {
                $this->form_validation->set_message('valid_card_no', sprintf(_('Branch %s Not IN Aceess Controller'), $data['branch_id']));

                return false;
            }

            $this->load->model('User');
            $content = $this->User->get_content($data['card_no']);

            if (empty($content)) {
                $this->form_validation->set_message('valid_card_no', sprintf(_('Branch %s and Card %s Not Exists'), $data['branch_id'], $data['card_no']));

                return false;
            }
            $this->user_id = $content['id'];

            $this->load->model('EntranceNotUser');
            $this->EntranceNotUser->user_id=$this->user_id;
            $not_user_exists=$this->EntranceNotUser->get_count();
            
            if(!empty($not_user_exists)) {
                $this->form_validation->set_message('valid_card_no', sprintf(_('The %s field is required.'), _('Branch')));

                return false;
            }

            return true;
        }
    }

    protected function set_form_validation($id = null)
    {
        if ($this->input->post('user_id')) { // 일반적인 경우
            $this->form_validation->set_rules('user_id', _('User'), 'required|integer');
            $this->form_validation->set_rules('date', _('Date'), 'required|callback_valid_date|callback_valid_not_future_day');
        } else {  // 입출입장치 DB trigger에서 전송된 경우, 또는 리더기 없는 사람 입력
            $this->form_validation->set_rules('branch_id', _('Branch'), 'integer');
            $this->form_validation->set_rules('card_no', _('Access Card No'), 'required|trim|callback_valid_card_no');
        }
    }

    protected function get_error_messages()
    {
        $message = parent::get_error_messages();
        $message['valid_not_future_day'] = _('The %s field must not future day.');

        return $message;
    }

    public function valid_not_future_day($day)
    {
        $insert_date = new DateTime($day, $this->timezone);
        $today = new DateTime($this->date . ' 23:59:59', $this->timezone);

        $date_diff = $today->diff($insert_date);

        if ($date_diff->format('%R') == '+') {
            return false;
        } else {
            return true;
        }
    }

    public function out($id)
    {
        $this->load->model($this->model);
        $this->{$this->model}->entrance_card_no = true;
        $content = $this->{$this->model}->get_content($id);

        if ($this->input->post('return_url')) {
            if ($this->{$this->model}->update(array('out_time' => $this->now, 'id' => $id))) {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'success', 'message' => sprintf(_('Successfully Out User(%s)'), $content['name']), 'redirect_path' => $this->delete_redirect_path($content)));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'success', 'message' => sprintf(_('Successfully Out User(%s)'), $content['name'])));
                    redirect('/');
                }
            }
        } else {
            $this->return_data['data']['content'] = $content;
            if ($this->format == 'html') {
                $this->layout->render('/entrances/out', $this->return_data);
            } else {
                echo json_encode(array('result' => 'error', 'message' => validation_errors()));
            }
        }
    }
}
