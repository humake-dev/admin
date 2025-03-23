<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Product_categories extends SL_Controller
{
    protected $model = 'ProductCategory';
    protected $permission_controller = 'Products';

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        $this->{$this->model}->type = array('product', 'sports_wear');
        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function set_add_form_data()
    {
        $this->index_data();
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data();
        $this->return_data['data']['content'] = $content;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Title'), 'required|min_length[1]|max_length[60]');
    }
}
