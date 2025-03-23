<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Permissions extends SL_Controller
{
    protected $model = 'Permission';

    protected function permission_check()
    {
        if ($this->session->userdata('role_id') != 1) {
            show_error('You do not have access to this section');
        }
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
        $this->form_validation->set_rules('controller', _('Controller'), 'required');
        $this->form_validation->set_rules('action', _('Action'), 'required');
    }

    protected function add_redirect_path($id)
    {
        return $this->router->fetch_class() . '?id=' . $id;
    }
}
