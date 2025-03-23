<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Basics extends SL_Controller
{
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
        $this->form_validation->set_rules('title', _('Title'), 'required');
        $this->form_validation->set_rules('description', _('Description'), 'trim');
    }

    protected function add_redirect_path($id)
    {
        return $this->router->fetch_class() . '?id=' . $id;
    }
}
