<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Order_Ends extends SL_Controller
{
    protected $model = 'OrderEnd';
    protected $parent_model = 'Order';
    protected $permission_controller = 'orders';

    protected function set_add_form_data()
    {
        $parent_id = $this->uri->segment(3);

        $this->load->model($this->parent_model);
        $content = $this->{$this->parent_model}->get_content($parent_id);

        $this->return_data['data']['content'] = $content;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('order_id', _('Order'), 'required|integer');
    }

    protected function add_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return $_SERVER['HTTP_REFERER'];
        }
    }

    protected function insert_complete_message($id)
    {
        return _('Successfully End Order');
    }
}
