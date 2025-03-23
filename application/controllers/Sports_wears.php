<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Product_extend.php';

class Sports_wears extends Product_extend
{
    protected $model = 'Product';
    protected $permission_controller = 'products';

    protected function permission_check()
    {
        if (in_array($this->router->fetch_method(), array('index', 'view'))) {
            if ($this->Acl->has_permission('rent_sws', 'write')) {
                return true;
            }
        }

        parent::permission_check();
    }

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        if ($this->input->get('branch_id')) {
            $this->{$this->model}->branch_id = $this->input->get('branch_id');
        }

        $this->{$this->model}->type = 'sports_wear';
        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }
}
