<?php

trait Ac_sync
{
    protected $access_control = true;

    protected function sync_access_controller($user_id)
    {
        $this->load->model('AccessController');
        $ac_list = $this->AccessController->get_index();

        if (empty($ac_list['total'])) {
            return false;
        }

        $c_data = array();

        $this->load->model('Enroll');
        $this->Enroll->user_id = $user_id;
        $this->Enroll->get_current_only = true;
        $this->Enroll->primary_only = true;
        $this->Enroll->stopped = false;

        $list = $this->Enroll->get_index(1000, 0, 'end_date');

        if (empty($list['total'])) {
            $this->load->model('User');
            $this->User->no_branch = true;
            $content = $this->User->get_content($user_id);

            $c_data['user_id'] = $content['id'];
            $c_data['user_name'] = $content['name'];
            $c_data['card_no'] = $content['card_no'];
            $c_data['phone'] = $content['phone'];
            $c_data['transaction_date'] = $content['registration_date'];

            if (!empty($content['gender'])) {
                $c_data['gender'] = $content['gender'];
            }

            if (!empty($content['email'])) {
                $c_data['email'] = $content['email'];
            }

            //$result = $this->ac_delete($ac_list, $user_id);
            $result = $this->ac_stop($ac_list, $c_data);
        } else {
            $content = $list['list'][0];

            $c_data['user_id'] = $content['user_id'];
            $c_data['user_name'] = $content['user_name'];
            $c_data['card_no'] = $content['card_no'];
            $c_data['phone'] = $content['phone'];
            $c_data['product_id'] = $content['product_id'];

            if (!empty($content['gender'])) {
                $c_data['gender'] = $content['gender'];
            }

            if (!empty($content['email'])) {
                $c_data['email'] = $content['email'];
            }

            $c_data['transaction_date'] = $content['registration_date'];
            $c_data['start_date'] = $content['start_date'];
            $c_data['end_date'] = $content['end_date'];

            $result = $this->ac_insert($ac_list, $c_data);
        }

        return $result;
    }

    protected function ac_sync_sh(array $ac_content, array $order_list, array $data)
    {
        $this->load->model($ac_content['model']);

        return $this->{$ac_content['model']}->sync($order_list, $data);
    }

    /*  same user, enroll, rent  start */
    protected function ac_insert(array $ac_list, array $data)
    {
        if (empty($ac_list['total'])) {
            return false;
        }

        foreach ($ac_list['list'] as $ac_content) {
            if (!$this->validate_card_ist($data['card_no'])) {
                return false;
            }
            $data['send_ip'] = $ac_content['send_ip'];
            $data['dest_ip'] = $ac_content['dest_ip'];
            $data['device_id'] = $ac_content['device_id'];

            $this->load->model($ac_content['model']);
            $this->{$ac_content['model']}->insert($data);
        }

        return true;
    }

    protected function ac_stop(array $ac_list, array $data)
    {
        if (empty($ac_list['total'])) {
            return false;
        }

        foreach ($ac_list['list'] as $ac_content) {
            if (!$this->validate_card_ist($data['card_no'])) {
                return false;
            }
            
            $data['send_ip'] = $ac_content['send_ip'];
            $data['dest_ip'] = $ac_content['dest_ip'];
            $data['device_id'] = $ac_content['device_id'];

            $this->load->model($ac_content['model']);
            $this->{$ac_content['model']}->stop($data);
        }

        return true;
    }

    protected function ac_delete(array $ac_list, $id)
    {
        if (empty($ac_list['total'])) {
            return false;
        }

        foreach ($ac_list['list'] as $ac_content) {
            $this->load->model($ac_content['model']);
            $this->{$ac_content['model']}->delete($id);
        }

        return true;
    }

    protected function validate_card_ist($card_no)
    {
        if (strlen($card_no) != 10) {
            return false;
        }

        if (!ctype_digit(strval($card_no))) {
            return false;
        }

        return true;
    }
}
