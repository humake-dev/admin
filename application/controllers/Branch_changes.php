<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Branch_changes extends SL_Controller
{
    protected $model = 'Branch';

    public function change($branch_id)
    {
        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($branch_id);

        $this->session->set_userdata(array('branch_id' => $content['id'], 'branch_name' => $content['title']));

        if($this->session->userdata('role_id')==1) {
            $this->session->set_userdata(array('center_id' => $content['center_id']));
        }

        redirect('/');
    }
}
