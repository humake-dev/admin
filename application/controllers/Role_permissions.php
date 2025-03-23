<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Role_permissions extends SL_Controller
{
    protected $model = 'RolePermission';

    protected function permission_check()
    {
        if ($this->session->userdata('role_id') != 1) {
            show_error('You do not have access to this section');
        }
    }

    protected function index_data($category_id = null)
    {
        $this->set_page();

        $this->load->model($this->model);
        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        $this->load->model('Role');
        $this->return_data['data']['role'] = $this->Role->get_index(1000, 0);

        $this->load->model('Permission');
        $this->return_data['data']['permission'] = $this->Permission->get_index(1000, 0);

        //$this -> output -> cache(1200);
        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function set_add_form_data()
    {
        $this->index_data();
    }

    protected function set_insert_data($data)
    {
        $data['role_id'] = $data['role'];
        $data['permission_id'] = $data['permission'];

        return $data;
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data();
        $this->return_data['data']['content'] = $content;
    }

    protected function set_update_data($id, $data)
    {
        $data = $this->set_insert_data($data);
        $data['id'] = $id;

        return $data;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('role', _('Role'), 'required');
        $this->form_validation->set_rules('permission', _('Permission'), 'required');
    }

    protected function add_redirect_path($id)
    {
        return $this->router->fetch_class() . '?id=' . $id;
    }
}
