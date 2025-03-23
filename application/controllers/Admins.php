<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_photo.php';

class Admins extends SL_photo
{
    protected $model = 'Employee';
    protected $script = 'admin/index.js';
    protected $directory = 'Employee';
    protected $check_permission = false;
    protected $search_start_date;
    protected $search_end_date;
    protected $file_model='EmployeePicture';

    public function __construct()
    {
        parent::__construct();

        if (empty($this->directory)) {
            $this->directory = camelize(lcfirst($this->model));
        }

        //if (empty($this->category_directory)) {
        if (empty($this->session->userdata('admin_branch_id'))) {
            $this->category_directory = $this->session->userdata('branch_id');
        } else {
            $this->category_directory = $this->session->userdata('admin_branch_id');
        }
        //}
    }

    public function setting()
    {
        if ($this->input->post('member2-body')) {
            $this->session->userdata('member2-body', true);
        }

        if ($this->input->post('member2-body-delete')) {
            $this->session->unset_userdata('member2-body');
        }

        if ($this->format == 'html') {
        } else {
            echo json_encode(array('result' => 'success'));
        }
    }

    public function barcode($id)
    {
        $content = $this->get_view_content_data($id);
        $this->return_data['data']['content'] = $content;

        $this->script = 'admins/barcode.js';
        $this->render_format();
    }

    public function change_password()
    {
        $this->load->library('form_validation');
        $this->set_message();

        $other_edit_available = false;
        if ($this->session->userdata('role_id') < 3) {
            $other_edit_available = true;
        }

        if ($other_edit_available) {
            $this->form_validation->set_rules('employee_id', _('Employee'), 'integer');

            if (!$this->input->post('employee_id')) {
                $this->form_validation->set_rules('current_password', _('Current Password'), 'min_length[4]|max_length[40]|callback_check_password');
            }
        } else {
            $this->form_validation->set_rules('current_password', _('Current Password'), 'min_length[4]|max_length[40]|callback_check_password');
        }
        $this->form_validation->set_rules('new_password', _('New Password'), 'required|min_length[4]|max_length[40]');
        $this->form_validation->set_rules('new_password_confirm', _('New Password Confirm'), 'required|min_length[4]|max_length[40]|matches[new_password]');

        if ($other_edit_available and $this->input->get_post('employee_id')) {
            $this->load->model('Employee');
            $employee_content = $this->Employee->get_content($this->input->get_post('employee_id'));
        }

        if ($this->form_validation->run() == false) {
            if (!empty($employee_content)) {
                $this->return_data['data']['content'] = $employee_content;
            }

            $this->layout->render('/admins/change_password', $this->return_data);
        } else {
            $data = $this->input->post(null, true);

            if ($other_edit_available and $data['employee_id']) {
                $admin_id = $data['employee_id'];
            } else {
                $admin_id = $this->session->userdata('admin_id');
            }

            $this->load->model('Admin');
            $result = $this->Admin->update_encrypted_password($data['new_password'], $admin_id);

            if ($result) {
                if ($other_edit_available and $data['employee_id']) {
                    $this->session->set_flashdata('message', array('type' => 'success', 'message' => sprintf(_('Successfully Change Password %s'), $employee_content['name'])));
                    redirect('/employees/view/' . $data['employee_id']);
                } else {
                    $this->session->set_flashdata('message', array('type' => 'success', 'message' => _('Successfully Change Password')));
                    redirect('/admins/edit');
                }
            } else {
                redirect('/admins/change-password');
            }
        }
    }

    /* public function check_available()
    {
        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($this->input->get_post('employee_id'));

        if($this->session->userdata('role_id')<=$content['role_id']) {
            return false;
        }
    } */

    public function check_password($password)
    {
        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($this->session->userdata('admin_id'));

        $this->load->model('AdminLogin');
        if ($this->AdminLogin->login($content['uid'], $password)) {
            return true;
        } else {
            return false;
        }
    }

    private function get_my_user($count = false)
    {
        $this->load->model('MyUserFc');
        $this->MyUserFc->search_start_date = $this->search_start_date;
        $this->MyUserFc->search_end_date = $this->search_end_date;

        if($this->input->get('refund')) {
            $this->MyUserFc->refund=true;
        }

        if ($count) {
            $users = $this->MyUserFc->get_count();
        } else {
            $users = $this->MyUserFc->get_index($this->per_page, $this->page);
        }

        return $users;
    }

    private function get_my_pt($count = false)
    {
        $this->load->model('MyUserTrainer');
        $this->MyUserTrainer->search_start_date = $this->search_start_date;
        $this->MyUserTrainer->search_end_date = $this->search_end_date;

        if($this->input->get('pt_type')) {
            $this->MyUserTrainer->pt_type=$this->input->get('pt_type');
        }

        if ($count) {
            $enrolls = $this->MyUserTrainer->get_count();
        } else {
            $enrolls = $this->MyUserTrainer->get_index($this->per_page, $this->page);
        }

        return $enrolls;
    }

    protected function render_index_resource()
    {
        if ($this->router->fetch_method() == 'barcode') {
            $this->layout->add_js('plugins/jquery-barcode.js');
        }

        $this->layout->add_js($this->script . '?version=' . $this->assets_version);
    }

    public function my_profit()
    {
        $this->set_page();

        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->form_validation->set_rules('employee_name', _('Employee'), 'trim');
        $this->form_validation->set_rules('employee_id', _('Employee'), 'integer');
        $this->form_validation->set_rules('date_p', _('date_p'), 'in_list[0,7,30,90,180,365,all]');

        if ($this->input->get('start_date')) {
            $this->form_validation->set_rules('start_date', _('Start Date'), 'callback_valid_date');
        }
        if ($this->input->get('end_date')) {
            $this->form_validation->set_rules('end_date', _('End Date'), 'callback_valid_date');
        }

        $type = 'all';
        if ($this->input->get('trainer')) {
            $type = 'trainer';
        }

        // 기본 년,월
        $date_obj = new DateTime('now', $this->timezone);
        $year = $date_obj->format('Y');
        $month = $date_obj->format('m');
        $end_date = $date_obj->format('Y-m-d');
        $date_p = 'all';

        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
            $display_start_date = $start_date;
        } else {
            $start_date = '2010-01-01';
            $display_start_date = '';
        }

        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        }

        $this->return_data['search_data']['date_p'] = $date_p;
        $this->return_data['search_data']['start_date'] = $display_start_date;
        $this->return_data['search_data']['end_date'] = $end_date;

        $this->search_start_date = $start_date;
        $this->search_end_date = $end_date;

        $this->load->model('Employee');
        $employee = $this->Employee->get_content($this->session->userdata('admin_id'));

        $type = 'enroll';

        $this->load->model('Enroll');
        $this->Enroll->trainer_id = $this->session->userdata('admin_id');
        $this->Enroll->lesson_type = 4;
        $enrolls = $this->Enroll->get_index(10000, 0);

        $total_left_quantity = 0;
        $total_left_commission = 0;
        $total_use_quantity = 0;
        if ($enrolls['total']) {
            foreach ($enrolls['list'] as $enroll) {
                $left_quantity = $enroll['quantity'] - $enroll['use_quantity'];
                $total_left_quantity += $left_quantity;
                $total_use_quantity += $enroll['use_quantity'];
                if ($enroll['quantity'] and $left_quantity) {
                    $total_left_commission += ($enroll['price'] / $enroll['quantity']) * ($employee['commission_rate'] / 100) * $left_quantity;
                }
            }
        }

        switch ($this->input->get('type')) {
            case 'pt':
                if ($this->input->get('enroll_id')) {
                    $this->load->model('EnrollUseLog');
                    $this->EnrollUseLog->enroll_id = $this->input->get('enroll_id');
                    $this->return_data['data'] = $this->EnrollUseLog->get_index();
                } else {
                    $this->return_data['data'] = $this->get_my_pt();
                }
                break;
            default :
                if ($this->session->userdata('is_fc')) {
                    $this->return_data['data'] = $this->get_my_user();
                } else {
                    if ($this->input->get('enroll_id')) {
                        $this->load->model('EnrollUseLog');
                        $this->EnrollUseLog->enroll_id = $this->input->get('enroll_id');
                        $this->return_data['data'] = $this->EnrollUseLog->get_index();
                    } else {
                        $this->return_data['data'] = $this->get_my_pt();
                    }
                }
        }

        $this->return_data['data']['content'] = $employee;


        if ($employee['is_fc']) {
            $this->load->model('Account');
            $this->return_data['data']['total_fc_sales'] = $this->Account->get_fc_total_sales($start_date, $end_date);
            $this->return_data['data']['total_fc_refund'] = $this->Account->get_fc_total_refund($start_date, $end_date);
            $this->return_data['data']['total_fc_profit']=$this->return_data['data']['total_fc_sales'] - $this->return_data['data']['total_fc_refund'];                          

            $this->return_data['data']['user_count'] = $this->get_my_user(true);            
        }
        
        if ($employee['is_trainer']) {
            $this->return_data['data']['pt_count'] = $this->get_my_pt(true);
        }

        $this->setting_pagination(array('total_rows' => $this->return_data['data']['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->form_validation->run();

        $this->render_format();
    }

    protected function set_update_data($id, $data)
    {
        $data = parent::set_update_data($id, $data);

        if (empty($data['email'])) {
            unset($data['email']);
        }

        return $data;
    }

    public function edit($id = null)
    {
        $id = $this->session->userdata('admin_id');

        $this->load->library('form_validation');
        $this->set_form_validation($id);
        $this->set_message();

        $before_content = $this->get_view_content_data($id);

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
                $this->set_edit_form_data($before_content);
                $this->render_format();
            } else {
                echo json_encode(array('result' => 'error', 'message' => validation_errors()));
            }
        } else {
            $data = $this->input->post(null, true);
            $data['edit_content'] = $before_content;

            $data = $this->set_update_data($id, $data);

            if ($this->{$this->model}->update($data)) {
                $this->after_update_data($id, $data);

                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'success', 'message' => $this->update_complete_message($id), 'redirect_path' => $this->edit_redirect_path($id)));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'success', 'message' => $this->update_complete_message($id)));
                    redirect('/admins/edit');
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'error', 'message' => _('Update Fail')));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('Update Fail')));
                    redirect($this->router->fetch_class() . '/edit/' . $id);
                }
            }
        }
    }

    protected function after_update_data($id, $data)
    {
        parent::after_update_data($id, $data);

        $upload_do = false;
        foreach ($_FILES['photo']['error'] as $index => $error) {
            if ($error) {
                unset($_FILES['photo']['name'][$index]);
                unset($_FILES['photo']['type'][$index]);
                unset($_FILES['photo']['tmp_name'][$index]);
                unset($_FILES['photo']['error'][$index]);
                unset($_FILES['photo']['size'][$index]);
                $upload_do = true;
            }
        }

        if (count($_FILES['photo']['error'])) {
            $picture = $this->update_file($this->session->userdata('admin_id'), false);

            $this->session->set_userdata(array('admin_picture' => $picture));
        }
    }

    public function update_file($id, $redirect = true)
    {
        $this->load->model('EmployeePicture');

        try {
            $upload_datas = $this->file_upload();

            foreach ($upload_datas as $upload_data) {
                $upload_file_data = array();
                $upload_file_data['picture_url'] = $upload_data['file_name'];
                $upload_file_data['admin_id'] = $id;

                if (!$this->EmployeePicture->insert($upload_file_data)) {
                    throw new Exception('Error Processing Request', 1);
                }
            }

            if ($this->format == 'json') {
                echo json_encode(array('result' => 'success', 'photo' => getPhotoPath(lcfirst($this->directory), $this->category_directory, $upload_data['file_name'], 'large')));
            } else {
                return $upload_file_data['picture_url'];
            }
        } catch (exception $e) {
            $error = $e->getMessage();

            if ($this->format == 'json') {
                echo json_encode(array('result' => 'error', 'message' => $e->getMessage()));
            } else {
                echo $error;
            }

            return false;
        }
    }

    public function unique_email($email = null, $id = null)
    {
        if (empty(trim($email))) {
            return true;
        }

        $this->load->model('Employee');

        return $this->Employee->check_unique_email($email, $id);
    }

    protected function get_error_messages()
    {
        $message = parent::get_error_messages();
        $message['check_password'] = '비밀번호가 맞지 않습니다';
        $message['unique_email'] = $message['is_unique'];

        return $message;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('name', _('Name'), 'required|min_length[2]|max_length[60]');
        $this->form_validation->set_rules('email', _('Email'), 'valid_email|callback_unique_email[' . $this->session->userdata('admin_id') . ']');
    }
}
