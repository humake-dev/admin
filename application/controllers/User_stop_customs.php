<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class User_stop_customs extends SL_Controller
{
    protected $permission_controller = 'users';
    protected $model = 'UserStopCustom';

    protected function set_add_form_data()
    {
        $enroll_content=$this->get_enroll_content_by_order_id($this->input->get_post('order_id'));
        $content=array('user_id'=>$enroll_content['user_id'],'order_id'=>$enroll_content['order_id'],'custom_days'=>$enroll_content['total_stop_day_count']);

        $this->return_data['data']['content'] = $content;
    }

    protected function set_insert_data($data)
    {
        $enroll_content=$this->get_enroll_content_by_order_id($data['order_id']);
        $data['custom_days']=$data['custom_days']-$enroll_content['total_stop_day_count'];
        
        return $data;
    }

    protected function set_edit_form_data(array $content)
    {
        $enroll_content=$this->get_enroll_content_by_order_id($this->input->get_post('order_id'));

        $this->load->model($this->model);
        $content=$this->{$this->model}->get_content_by_parent_id($enroll_content['order_id']);

        $content['user_id']=$enroll_content['user_id'];
        $content['custom_days']=$enroll_content['total_stop_day_count']+$content['custom_days'];

        $this->return_data['data']['content'] = $content;
    }

    protected function set_update_data($id, $data)
    {
        return $this->set_insert_data($data);
    }   

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('custom_days', _('User Stop Custom Days'), 'required|numeric');
    }

    private function get_enroll_content_by_order_id($order_id)
    {
        $this->load->model('Enroll');
        $this->Enroll->order_id = $order_id;
        $this->Enroll->primary_only = true;
        $enrolls=$this->Enroll->get_index();

        if(empty($enrolls['total'])) {
            
            exit;
        } else {
            $enroll_content=$enrolls['list'][0];
        }

        return $enroll_content;
    }
}
