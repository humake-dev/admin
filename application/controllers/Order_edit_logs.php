<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';

class Order_edit_logs extends SL_Controller
{
    use Search_period;

    protected $model = 'OrderEditLog';
    protected $permission_controller = 'users';

    protected function index_data($category_id = null)
    {
        $this->set_page();
        $this->set_search_form_validation();

        $this->load->model($this->model);
        $this->set_search();

        if ($this->input->get('field')) {
            $this->{$this->model}->field = $this->input->get('field');
        }

        if ($this->input->get('user_id')) {
            $this->{$this->model}->user_id = $this->input->get('user_id');
        }

        if ($this->input->get('product_id')) {
            $this->{$this->model}->product_id = $this->input->get('product_id');
        }

        if ($this->input->get('user_id') and $this->input->get('product_id')) {
            $this->load->model('Order');
            $this->Order->product_id=$this->input->get('product_id');
            $this->Order->user_id=$this->input->get('user_id');
            $order_list=$this->Order->get_index(1000);
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

        if(!empty($order_list)) {
            $this->return_data['data']['order_list']=$order_list;
        }

        $this->setting_pagination(array('total_rows' => $this->return_data['data']['total']));
        $this->return_data['search_data']['period_display'] = true;
        $this->form_validation->run();
    }

    protected function get_view_data($id)
    {
        $content = parent::get_view_data($id);

        $this->load->model('OrderEditLogField');
        $this->OrderEditLogField->parent_id = $content['id'];
        $change_logs = $this->OrderEditLogField->get_index(1000, 0);

        if ($change_logs['total']) {
            $exists_trainer_id = false;
            foreach ($change_logs['list'] as $value) {
                if ($value['field'] == 'trainer_id') {
                    $exists_trainer_id = true;
                }
            }

            if ($exists_trainer_id) {
                $this->load->model('Employee');
                foreach ($change_logs['list'] as $index => $value) {
                    if ($value['field'] == 'trainer_id') {
                        $origin_employee_name=_('Not Inserted');
                        $change_employee_name=_('Not Inserted');

                        if(!empty($value['origin'])) {
                            $origin_employee = $this->Employee->get_content($value['origin']);
                            $origin_employee_name=$origin_employee['name'];
                        }

                        if(!empty($value['change'])) {
                            $change_employee = $this->Employee->get_content($value['change']);
                            $change_employee_name=$change_employee['name'];
                        }

                        $change_logs['list'][$index]['origin'] = $origin_employee_name;
                        $change_logs['list'][$index]['change'] = $change_employee_name;
                    }
                }
            }
        }


        $this->return_data['data']['change_logs'] = $change_logs;
        $this->return_data['data']['content'] = $content;
    }

    protected function set_search_form_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->set_period_search_validation();
        $this->form_validation->set_rules('field', _('Field'), 'in_list[transaction_date,start_date,end_date,quantity]');
        $this->form_validation->set_rules('user_id', _('User'), 'integer');
        $this->form_validation->set_rules('product_id', _('Product'), 'integer');
        $this->form_validation->set_rules('order_id', _('Order'), 'integer');
    }
}
