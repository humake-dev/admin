<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Ac_sync.php';

class Temp_user_users extends SL_Controller
{
    use Ac_sync;

    protected $parent_model = 'TempUser';
    protected $model = 'TempUserUser';
    protected $permission_controller = 'users';

    protected function set_insert_data($data)
    {
        $this->load->model($this->parent_model);
        $content = $this->{$this->parent_model}->get_content($data['temp_user_id']);

        $data['user_id'] = $this->User->insert(array('name' => $content['name'], 'phone' => $content['phone']));

        $data['name'] = $content['name'];
        $data['phone'] = $content['phone'];
        $data['cotnent'] = $content['content'];

        return $data;
    }

    protected function after_insert_data($id, $data)
    {
        if ($this->return_data['common_data']['branch']['use_access_card']) {
            return true;
        }

        $dec_only = false;

        $this->load->model('AccessController');
        $this->AccessController->controller = 'ist';
        $ac_count = $this->AccessController->get_count();

        if ($ac_count) {
            $dec_only = true;
        }

        $card_no = $this->create_card_no($data['phone'], $dec_only);

        $this->load->model('UserAccessCard');
        $this->UserAccessCard->insert(array('user_id' => $data['user_id'], 'card_no' => $card_no));

        if (!empty($data['content'])) {
            $this->load->model('UserContent');
            $this->UserContent->insert(array('user_id' => $data['user_id'], 'content' => $content));
        }
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('temp_user_id', _('Temp User'), 'required|integer');
    }
}
