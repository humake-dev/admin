<?php

trait SMS_key
{
    // SMS 키값이 있으면 키값 사용 아니면 기본값 사용
    protected function get_sms_key()
    {
        switch($this->check_is_sms_key_new()) {
            case 2:
                $sms_key_set = array('sms_id' => SMS_ID2, 'sms_key' => SMS_KEY2);
                break;
            case 3:
                $sms_key_set = array('sms_id' => SMS_ID3, 'sms_key' => SMS_KEY3);
                break;
            case 4:
                $sms_key_set = array('sms_id' => SMS_ID4, 'sms_key' => SMS_KEY4);
                break;                
            default :
                $sms_key_set = array('sms_id' => SMS_ID, 'sms_key' => SMS_KEY);
        }

        return $sms_key_set;
    }

    public function check_is_sms_key_new() {
        if(in_array($this->session->userdata('branch_id'), array(4,6,7,36))) {
            return 2;
        }
        
        if(in_array($this->session->userdata('branch_id'), array(8,10,11,12,13,17))) {
            return 3;
        }

        if(in_array($this->session->userdata('branch_id'), array(3,5,14,15))) {
            return 4;
        }
        
        return 1;
    }
}
