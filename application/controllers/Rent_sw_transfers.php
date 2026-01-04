<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order_transfers.php';

class Rent_sw_transfers extends Order_transfers
{
    protected $parent_model = 'RentSw';
    protected $permission_controller = 'rents';
    protected $not_set_message = true;
    protected $type = 'rent_sw';

    protected function set_add_form_data()
    {
        $content=parent::set_add_form_data();

        $this->return_data['data']['form_url'] = '/rent-sws/transfer/' . $content['id'];
        $this->return_data['data']['return_url'] = '/home/rent-sws/' . $content['user_id'];
        $this->return_data['data']['product_name'] = _('Product');        
    }    

    // 상품날짜 처리
    protected function product_process(array $data)
    {
        $this->load->model($this->parent_model);

        if (empty($data['exists'])) {  // 없을때
            $order_content = $this->{$this->parent_model}->get_content_by_order_id($data['order_id']);

            $date_obj = new DateTime($data['transfer_date'], $this->timezone);
            $start_date = $date_obj->format('Y-m-d');
        } else { // 양수인이 해당 상품 있을때
            $order_content = $this->{$this->parent_model}->get_content($data['exists']['id']);

            $date_obj = new DateTime($order_content['end_date'], $this->timezone);
            $date_obj->modify('+1 Days');
            $start_date = $date_obj->format('Y-m-d');
        }

        $start_date = $date_obj->format('Y-m-d');
        $give_count = $data['give_count'] - 1;
        $date_obj->modify('+' . $give_count . ' Days');
        $end_date = $date_obj->format('Y-m-d');

        $this->{$this->parent_model}->update(array('id' => $order_content['id'], 'start_date' => $start_date, 'end_date' => $end_date));

        return $data;
    }

    protected function insert_complete_message($id)
    {
        if ($this->schedule) {
            return _('Successfully Schedule Transfer Rent');
        } else {
            return _('Successfully Transfer Rent');
        }
    }
}
