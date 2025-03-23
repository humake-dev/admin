<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Message_points extends SL_Controller
{
    protected $model = 'MessagePoint';
    protected $permission_controller = 'messages';

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        $branch_ids=$this->get_branch_ids();

        $this->{$this->model}->branch_id=$branch_ids;
        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('phone', _('Branch Phone'), 'required');
        $this->form_validation->set_rules('sms_available_point', _('SMS Send Point'), 'required|integer');
    }

    protected function edit_redirect_path($id)
    {
        return 'message-points';
    }

    public function delete_confirm($id)
    {
        $this->return_data['data']['id'] = $id;
        $this->layout->render('/message_points/delete', $this->return_data);
    }

    protected function delete_complete_message(array $content)
    {
        return _('Successfully Change Point Zero');
    }
}
