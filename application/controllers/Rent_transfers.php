<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order_transfers.php';

class Rent_transfers extends Order_transfers
{
    protected $parent_model = 'Rent';
    protected $permission_controller = 'rents';
    protected $not_set_message = true;
    protected $type = 'rent';
    protected $transfer_id = TRANSFER_RENT;

    protected function set_add_form_data()
    {
        $content=parent::set_add_form_data();
        
        $this->return_data['data']['form_url'] = '/rents/transfer/' . $content['id'];
        $this->return_data['data']['return_url'] = '/home/rents/' . $content['user_id'];
        $this->return_data['data']['product_name'] = _('Facility');
    }

    // 양도시 받는사람이 이미 있는 상품인지, 아닌지 확인
    protected function exists_product($product_id, $recipient_id, $branch_id=null)
    {
        $this->load->model('Facility');

        if(!empty($branch_id)) {
            $this->Facility->branch_id = $branch_id;
        }

        $facilities = $this->Facility->get_index(1000, 0);

        $product_id = array();
        if ($facilities['total']) {
            foreach ($facilities['list'] as $facility) {
                $product_id[] = $facility['product_id'];
            }
        }

        $this->load->model($this->parent_model);
        $this->{$this->parent_model}->user_id = $recipient_id;
        $this->{$this->parent_model}->product_id = $product_id;
        $this->{$this->parent_model}->get_not_end_only = true;
        
        if(!empty($branch_id)) {
            $this->{$this->parent_model}->branch_id = $branch_id;
        }
        
        $orders = $this->{$this->parent_model}->get_index(1000, 0, 'end_date');

        if (!$orders['total']) {
            return false;
        }

        return $orders['list'][0];
    }

protected function after_insert_data($id, $data)
{
    $this->load->model('Order');
    $data['order_content'] = $this->Order->get_content($data['order_id']);

    if ($this->schedule) {
        $enable=false;
        $this->load->model('OrderTransferSchedule');
        $this->OrderTransferSchedule->insert(array('order_transfer_id' => $id, 'schedule_date' => $data['schedule_date']));
    } else {
        $enable=true;
    }

    $this->load->model($this->parent_model);
    $data['rent_content'] = $this->{$this->parent_model}->get_content_by_order_id($data['order_id']);

    $this->product_process($data);
    $data['order_id'] = $this->insert_new_rent($data);
    
    $account_data = array(
        'user_id' => $data['recipient_id'],
        'order_id' => $data['order_content']['id'],
        'product_id' => 4,
        'account_category_id' => $this->transfer_id,
        'order_transfer_id' => $id,
        'enable' => $enable
    );

    if(!empty($data['commmission']) and !empty($data['payment_method'])) {
        switch($data['payment_method']) {
            case '1' :
                $account_data['cash']=$data['commmission'];
                break;
            case '4' :
                $account_data['cash']=$data['mix_cash'];
                $account_data['credit']=$data['mix_credit'];
                break;
            default : 
                $account_data['credit']=$data['commmission'];
        }
    }

    if (!empty($data['transaction_date'])) {
        $account_data['transaction_date']=$data['transaction_date'];
    }
    
    if ($this->trans_new_branch($id, $data)) {
        $account_data['branch_id'] = $data['branch_id'];
    }
    
    $this->insert_account($account_data);
}

    protected function insert_new_rent(array $data)
    {
        $this->load->model('Order');
        $new_order_id = $this->Order->insert(array('user_id' => $data['recipient_id'], 'transaction_date' => $this->today));

        if (empty($data['exists'])) { // 없을때
            $start_date = $data['transfer_date'];
            $end_date = $data['rent_content']['end_date'];
            $product_id=$data['rent_content']['product_id'];
        } else {
            $product_id=$data['exists']['product_id'];
            $date_obj = new DateTime($data['exists']['end_date'], $this->timezone);
            $date_obj->modify('+1 Days');

            $start_date = $date_obj->format('Y-m-d');
            $give_count = $data['give_count'] - 1;
            $date_obj->modify('+' . $give_count . ' Days');
            $end_date = $date_obj->format('Y-m-d');
        }

        $this->load->model('OrderProduct');
        $this->OrderProduct->insert(array('order_id' => $new_order_id, 'product_id' => $product_id));

        if ($data['give_count'] <= 60) {
            $insert_quantity = 1;
        } else {
            $insert_quantity = intval($data['give_count'] / 30);
        }
        
        $start_datetime = $start_date . ' 00:00:01';
        $end_datetime = $end_date . ' 23:59:59';

        $no = 0;

        $this->{$this->parent_model}->insert(array('facility_id' => $data['rent_content']['facility_id'], 'order_id' => $new_order_id, 'no' => $no, 'insert_quantity' => $insert_quantity, 'start_datetime' => $start_datetime, 'end_datetime' => $end_datetime));

        return $new_order_id;
    }

    protected function trans_new_branch($id, array $data)
    {
        if(!parent::trans_new_branch($id,$data)) {
            return false;
        }

        $this->load->model('Facility');
        $facility = $this->Facility->get_content_by_product_id($data['product_id']);

        $this->load->model('Rent');
        $rent_id = $this->Rent->get_id_by_order_id($data['order_id']);
        $this->Rent->update(array('facility_id' => $facility['id'], 'id' => $rent_id));        
    }

    // 상품날짜 처리
    protected function product_process(array $data)
    {
        return $this->{$this->parent_model}->update(array('id' => $data['rent_content']['id'], 'end_datetime' => $data['transfer_date']));
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
