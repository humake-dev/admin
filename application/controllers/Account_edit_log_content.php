<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Account_edit_log_contents extends SL_Controller
{
    protected $model = 'AccountEditLog';
    protected $permission_controller = 'users';
    protected $script = 'content.js';

    protected function set_form_validation($id = null)
    {
        if ($this->router->fetch_method() == 'add') {
            $this->form_validation->set_rules('account_edit_log_id', _('Account Edit Log'), 'required|integer');
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
