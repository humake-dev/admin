<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Facility_categories extends SL_Controller
{
    protected $model = 'ProductCategory';
    protected $check_permission = true;

    protected function set_add_form_data()
    {
        $this->load->model($this->model);
        $this->index_data();
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data();
        $this->return_data['data']['content'] = $content;
    }

    protected function set_insert_data($data)
    {
        $data['type'] = 'course';

        return $data;
    }

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        $this->{$this->model}->type = 'course';
        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Title'), 'required|max_length[60]');
        $this->form_validation->set_rules('order_no', _('Order No'), 'required|integer');
    }

    protected function add_redirect_path($id)
    {
        return '/facility-categories?id=' . $id;
    }
}
