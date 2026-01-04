<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class User_heights extends SL_Controller
{
    protected $permission_controller = 'users';
    protected $model = 'UserHeight';

    protected function set_add_form_data()
    {
        $this->load->model('User');
        $content = $this->User->get_content($this->input->get_post('user_id'));

        $this->load->model($this->model);
        $this->{$this->model}->user_id = $content['id'];
        $bodies = $this->{$this->model}->get_index(1000, 0);

        if ($bodies['total']) {
            $this->return_data['data']['content'] = $bodies['list'][0];
        }

        $content['user_id'] = $content['id'];
        $this->return_data['data']['content'] = $content;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('height', _('Height'), 'required|numeric|less_than[300]|greater_than[10]');
    }

    protected function add_redirect_path($id)
    {
        return '/home/body-indexes/' . $this->input->get_post('user_id');
    }

    protected function edit_redirect_path($id)
    {
        return $this->add_redirect_path($id);
    }
}
