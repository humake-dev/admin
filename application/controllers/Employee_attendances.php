<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Employee_attendances extends SL_Controller
{
    protected $model = 'EmployeeAttendance';
    protected $permission_controller = 'employees';
    protected $check_permission = false;
    protected $employee_id;

    protected function login_check()
    {
        return true;
    }

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        if ($this->input->get('employee_id')) {
            $this->{$this->model}->employee_id = $this->input->get('employee_id');
        }

        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
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

        if (!empty($data['employee_id'])) {
            $data['admin_id'] = $data['employee_id'];
        } else {
            if (empty($data['employee_id']) and !empty($data['card_no'])) {
                $data['admin_id'] = $this->employee_id;

                if (!empty($data['branch_id'])) {
                        $date = new DateTime($data['date'] . $data['time'], $this->timezone);
                        $data['in_time'] = $date->format('Y-m-d H:i:s');
                }
            }
        }

        return $data;
    }

    public function valid_card_no($card_no)
    {
        $data = $this->input->post(null, true);

        if ($this->session->userdata('branch_id')) {
            $this->load->model('Employee');
            $content = $this->Employee->get_content_by_card_no($data['card_no'], $this->session->userdata('branch_id'));

            if (empty($content)) {
                $this->form_validation->set_message('valid_card_no', sprintf(_('Branch %s and Card %s Not Exists'), $this->session->userdata('branch_name'), $data['card_no']));

                return false;
            }

            $this->employee_id = $content['id'];

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

            $this->load->model('Employee');
             $content = $this->Employee->get_content($data['card_no']);

            if (empty($content)) {
                $this->form_validation->set_message('valid_card_no', sprintf(_('Branch %s and Card %s Not Exists'), $data['branch_id'], $data['card_no']));

                return false;
            }
            $this->employee_id = $content['id'];

            return true;
        }
    }

    protected function set_form_validation($id = null)
    {
        if ($this->input->post('employee_id')) { // 일반적인 경우
            $this->form_validation->set_rules('employee_id', _('Employee'), 'required|integer');
            $this->form_validation->set_rules('date', _('Date'), 'required|callback_valid_date|callback_valid_not_future_day');
        } else {  // 입출입장치 DB trigger에서 전송된 경우, 또는 리더기 없는 사람 입력
            $this->form_validation->set_rules('branch_id', _('Branch'), 'integer');
            $this->form_validation->set_rules('card_no', _('Access Card No'), 'required|trim|callback_valid_card_no');
        }

        if ($this->input->post('entrance_card')) {
            $this->form_validation->set_rules('entrance_card', _('Entrance Card'), 'trim|callback_valid_entrance_card');
        }
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

    protected function add_redirect_path($id)
    {
        return 'employees/attendances/' . $this->input->post('employee_id');
    }
}
