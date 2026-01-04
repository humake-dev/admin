<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';

class Account_edit_logs extends SL_Controller
{
    use Search_period;

    protected $model = 'AccountEditLog';
    protected $permission_controller = 'users';

    protected function index_data($category_id = null)
    {
        $this->set_page();
        $this->set_search_form_validation();

        $this->load->model($this->model);
        $this->set_search();

        if ($this->input->get('user_id')) {
            $this->{$this->model}->user_id = $this->input->get('user_id');
        }

        if ($this->input->get('product_id')) {
            $this->{$this->model}->product_id = $this->input->get('product_id');
        }

        if ($this->input->get('order_id')) {
            $this->{$this->model}->start_date = null;
            $this->{$this->model}->end_date = null;
            $this->{$this->model}->order_id = $this->input->get('order_id');
        }

        $this->return_data['data'] = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data']['user_list'] = $this->{$this->model}->get_user_list();

        $this->load->model('Product');
        $this->return_data['data']['product_list'] = $this->Product->get_index();

        $this->setting_pagination(array('total_rows' => $this->return_data['data']['total']));
        $this->return_data['search_data']['period_display'] = true;
        $this->form_validation->run();
    }

    protected function get_view_data($id)
    {
        $content = parent::get_view_data($id);

        $this->load->model('AccountEditLogField');
        $this->AccountEditLogField->parent_id = $content['id'];
        $change_logs = $this->AccountEditLogField->get_index(1000, 0);

        $this->return_data['data']['change_logs'] = $change_logs;
        $this->return_data['data']['content'] = $content;
    }

    protected function set_search_form_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->set_period_search_validation();
        $this->form_validation->set_rules('user_id', _('User'), 'integer');
        $this->form_validation->set_rules('product_id', _('Product'), 'integer');
        $this->form_validation->set_rules('account_id', _('Account'), 'integer');
    }
}
