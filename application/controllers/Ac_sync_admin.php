<?php

trait Ac_sync_admin
{
    protected $access_control = true;

    protected function sync_access_controller($employee_id, $type = 'insert')
    {
        $this->load->model('AccessController');
        $ac_list = $this->AccessController->get_index();

        if (empty($ac_list['total'])) {
            return false;
        }

        $c_data = array();
        
        switch ($type) {
            case 'delete':
                $result = $this->ac_delete($ac_list, $employee_id);
                break;
            case 'update':
                $this->load->model('Employee');
                $content = $this->Employee->get_content($employee_id);

                $c_data['user_id'] = $content['id'];
                $c_data['user_name'] = $content['name'];
                $c_data['card_no'] = $content['card_no'];
                $c_data['transaction_date'] = $content['hiring_date'];

                $c_data['start_date'] = $this->today;
                $c_data['end_date'] = $this->max_date;
                $result = $this->ac_insert($ac_list, $c_data);
                break;
            case 'insert': // 'insert'
                $this->load->model('Employee');
                $content = $this->Employee->get_content($employee_id);

                $c_data['user_id'] = $content['id'];
                $c_data['user_name'] = $content['name'];
                $c_data['card_no'] = $content['card_no'];
                $c_data['transaction_date'] = $content['hiring_date'];
                $c_data['start_date'] = $this->today;
                $c_data['end_date'] = $this->max_date;

                $result = $this->ac_insert($ac_list, $c_data);
        }

        return $result;
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

    protected function ac_delete($ac_list, $id)
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
