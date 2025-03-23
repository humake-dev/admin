<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Notices extends SL_Controller
{
    protected $model = 'Notice';

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
        $this->form_validation->set_rules('title', _('Title'), 'required|min_length[3]|max_length[60]');
        $this->form_validation->set_rules('content', _('Content'), 'required|trim');
    }
}
