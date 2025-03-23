<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';

class Enroll_use_logs extends SL_Controller
{
    use Search_period;

    protected $model = 'EnrollUseLog';
    protected $permission_controller = 'enrolls';
    protected $use_index_content = true;

    protected function index_data($category_id = null)
    {
        $this->set_search_form_validation();
        $this->form_validation->set_rules('employee_name', _('Employee'), 'trim');
        $this->form_validation->set_rules('employee_id', _('Employee'), 'integer');

        $this->load->model($this->model);
        $this->set_page();
        $this->set_search();

        if ($this->category_model) {
            $category = $this->get_category($category_id);

            if ($category['total']) {
                $this->{$this->model}->category_id = $category['current_id'];
            }
        }

        if ($this->input->get('user_id')) {
            $user_id = $this->input->get('user_id');

            $this->load->model('User');
            $user_content = $this->User->get_content($user_id);

            if (!empty($user_content)) {
                if ($this->input->get('enroll_id')) {
                    $this->load->model('Enroll');
                    $enroll_content = $this->Enroll->get_content($this->input->get('enroll_id'));
                    $this->{$this->model}->enroll_id = $this->input->get('enroll_id');
                }

                $this->{$this->model}->user_id = $user_content['id'];
            }
        }

        if ($this->input->get('enroll_id')) {
            if(empty($enroll_content)) {
                $this->load->model('Enroll');
                $enroll_content = $this->Enroll->get_content($this->input->get('enroll_id'));
            }

            $this->{$this->model}->enroll_id = $this->input->get('enroll_id');
        }

        if ($this->input->get('employee_id')) {
            $this->{$this->model}->trainer_id = $this->input->get('employee_id');

            $this->load->model('Employee');
            $employee_content = $this->Employee->get_content($this->input->get('employee_id'));
        }

        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        if ($this->use_index_content) {
            $this->return_data['data']['content'] = $this->get_list_view_data($list);
        }

        if (!empty($user_content)) {
            $this->return_data['data']['user'] = $user_content;

            $this->load->model('Enroll');
            $this->Enroll->lesson_type = 4;
            $this->Enroll->user_id = $user_content['id'];
            $this->Enroll->get_current_only = false;
            $enrolls = $this->Enroll->get_index();
        }

        if (!empty($employee_content)) {
            $this->return_data['data']['employee'] = $employee_content;
        }

        if (!empty($enrolls)) {
            $this->return_data['data']['enrolls'] = $enrolls;
        }

        if (!empty($enroll_content)) {
            $this->return_data['data']['enroll_content'] = $enroll_content;
        }

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->form_validation->run();
        $this->script = 'enroll-use-logs/index.js';
    }

    protected function set_search_form_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->set_period_search_validation();
    }

    protected function set_edit_form_data(array $content)
    {
        $this->load->model('Enroll');
        $this->Enroll->user_id = $content['user_id'];
        $this->Enroll->lesson_type = array(2, 3, 4, 5);
        $this->Enroll->get_start_only = true;
        $this->return_data['data']['enroll_list'] = $this->Enroll->get_index(1000, 0);

        $this->return_data['data']['content'] = $content;
    }

    protected function after_update_data($id, $data)
    {
        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($id);

        if (empty($content['account_id'])) {
            $account_source = array('account_category_id' => ADD_COMMISSION, 'type' => 'O', 'branch_id' => $this->session->userdata('branch_id'), 'user_id' => $content['user_id'], 'cash' => $data['commission'], 'enroll_id' => $content['enroll_id'], 'employee_id' => $content['manager_id']);

            $this->load->model('Enroll');
            $enroll_content = $this->Enroll->get_content($content['enroll_id']);

            $account_source['course_id'] = $enroll_content['course_id'];
            $account_source['order_id'] = $enroll_content['order_id'];

            $this->load->model('Account');
            $account_id = $this->Account->insert($account_source);

            $this->{$this->model}->update(array('id' => $id, 'account_id' => $account_id));
        } else {
            if ($data['commission'] != $data['edit_content']['commission']) {
                $this->load->model('Account');
                $this->Account->update(array('id' => $data['edit_content']['account_id'], 'cash' => $data['commission']));
            }
        }
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('enroll_id', _('Enroll'), 'required|integer');
        $this->form_validation->set_rules('commission', _('Commission'), 'integer');
    }

    public function my_profit()
    {
        $this->set_page();

        $this->load->model('Employee');
        $employee = $this->Employee->get_content($this->session->userdata('admin_id'));

        $this->load->model('Account');
        $total_commission = $this->Account->get_total_commision($this->session->userdata('admin_id'));

        $this->load->model('EnrollUseLog');
        $lists = $this->EnrollUseLog->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $lists;

        $this->return_data['data']['commission_rate'] = $employee['commission_rate'];
        $this->return_data['data']['total_commission'] = $total_commission;
        $this->return_data['data']['total_left_commission'] = $total_left_commission;

        $this->setting_pagination(array('total_rows' => $this->return_data['data']['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->render_format();
    }

    protected function edit_redirect_path($id)
    {
        return $_SERVER['HTTP_REFERER'];
    }
}
