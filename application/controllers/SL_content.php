<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class SL_content extends SL_Controller
{
    protected $model;
    protected $permission_controller = 'users';
    protected $script = 'content.js';
    protected $parent_id_name;
    protected $default_view_directory = 'memo';

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        if ($this->input->get('parent_id')) {
            $this->{$this->model}->{$this->parent_id_name} = $this->input->get('parent_id');
        }
        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function set_add_form_data()
    {
        $this->return_data['data']['parent_id_name'] = $this->parent_id_name;
    }

    protected function set_form_validation($id = null)
    {
        if ($this->router->fetch_method() == 'add') {
            $this->form_validation->set_rules($this->parent_id_name, _('Parent'), 'required|integer');
        }
        $this->form_validation->set_rules('content', _('Memo'), 'required|trim');
    }

    protected function add_redirect_path($id)
    {
        return $_SERVER['HTTP_REFERER'];
    }

    protected function edit_redirect_path($id)
    {
        return $_SERVER['HTTP_REFERER'];
    }

    protected function delete_redirect_path(array $content)
    {
        return $_SERVER['HTTP_REFERER'];
    }
}
