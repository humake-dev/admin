<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order_transfers.php';

class Enroll_transfers extends Order_transfers
{
    protected $parent_model = 'Enroll';
    protected $model = 'OrderTransfer';
    protected $permission_controller = 'enrolls';
    protected $type = 'enroll';
    protected $default_view_directory = 'enroll_transfers';
    protected $transfer_id = TRANSFER_ENROLL;
    
    protected function set_add_form_data()
    {
        $content=parent::set_add_form_data();
        
        $this->load->model('Rent');
        $this->Rent->user_id=$content['user_id'];
        $this->Rent->start_date=$content['start_date'];
        $this->Rent->end_date=$content['end_date'];
        $same_rent=$this->Rent->get_index();


        $this->load->model('RentSw');
        $this->RentSw->user_id=$content['user_id'];
        $this->RentSw->start_date=$content['start_date'];
        $this->RentSw->end_date=$content['end_date'];
        $same_rent_sw=$this->RentSw->get_index();
        
        if(!empty($same_rent['total'])) {
            $this->return_data['data']['same_rent'] = $same_rent['list'][0];
        }
        
        if(!empty($same_rent_sw['total'])) {
            $this->return_data['data']['same_rent_sw'] = $same_rent_sw['list'][0];
        }
        
        $this->return_data['data']['form_url'] = '/enrolls/transfer/' . $content['id'];
        $this->return_data['data']['return_url'] = '/home/enrolls/' . $content['user_id'];
        $this->return_data['data']['product_name'] = _('Course');
    }

    // 양도시 받는사람이 이미 있는 상품인지, 아닌지 확인
    protected function exists_product($product_id, $recipient_id, $branch_id=null)
    {
        $this->load->model('Course');
        $course = $this->Course->get_content_by_product_id($product_id);

        $this->load->model($this->parent_model);
        $this->{$this->parent_model}->user_id = $recipient_id;
        $this->{$this->parent_model}->lesson_type = $course['lesson_type'];
        $this->{$this->parent_model}->get_not_end_only = true;

        if(!empty($branch_id)) {
            $this->{$this->parent_model}->branch_id = $branch_id;
        }

        $orders = $this->{$this->parent_model}->get_index(1000, 0, 'end_date');

        if (empty($orders['total'])) {
            return false;
        }

        return $orders['list'][0];
    }

    protected function set_insert_data($data)
    {
        $data = parent::set_insert_data($data);

        $this->load->model('Enroll');
        $content = $this->Enroll->get_content_by_order_id($data['order_id']);

        if ($content['lesson_type'] != 1) {
            $data['give_count'] = $content['quantity'] - $content['use_quantity'];
        }

        if(empty($data['transaction_date_is_today'])) {
            $data['transaction_date_is_today']=false;
        }

        if(empty($data['no_schedule'])) {
            $data['no_schedule']=false;
        }

        return $data;
    }

    // 상품날짜 처리
    protected function product_process(array $data)
    {
        $this->load->model('Enroll');
        $order_content = $this->Enroll->get_content_by_order_id($data['order_id']);

        if (empty($data['exists'])) {  // 없을때
            $date_obj = new DateTime($data['transfer_date'], $this->timezone);
            $start_date = $date_obj->format('Y-m-d');
            $have_datetime = $this->now;
        } else { // 양수인이 해당 상품 있을때
            $enroll_content = $this->Enroll->get_content($data['exists']['id']);

            $date_obj = new DateTime($enroll_content['end_date'], $this->timezone);
            $date_obj->modify('+1 Days');
            $start_date = $date_obj->format('Y-m-d');
            $have_datetime = $start_date . ' 00:00:01';
        }

        if ($order_content['lesson_type'] == 4) {
            $this->Enroll->update(array('id' => $order_content['id'], 'have_datetime' => $have_datetime));

            if (!empty($enroll_content['trainer_id']) and empty($enroll_content['commission'])) {
                $this->load->model('Employee');
                $employee = $this->Employee->get_content($enroll_content['trainer_id']);

                if (!empty($employee['commission_rate'])) { // 강사 수수료가 설정되어 있으면 아래공식으로 수수료 설정
                    $commission = ($enroll_content['price'] / $enroll_content['insert_quantity']) * ($employee['commission_rate'] / 100);

                    $this->load->model('EnrollCommission');
                    $this->EnrollCommission->insert(array('enroll_id'=>$enroll_content['id'],'commission'=>$commission));
                }
            }
        } else {
            $give_count = $data['give_count'] - 1;
            $date_obj->modify('+' . $give_count . ' Days');
            $end_date = $date_obj->format('Y-m-d');

            $this->Enroll->update(array('id' => $order_content['id'], 'have_datetime' => $have_datetime, 'start_date' => $start_date, 'end_date' => $end_date));
        }

        return $data;
    }

    protected function after_insert_data($id, $data)
    {
        parent::after_insert_data($id,$data);
        
        if(!empty($data['same_rent'])) {
            $this->load->model('Rent');
            $same_rent=$this->Rent->get_content_by_order_id($data['same_rent']);

            $rent_data=array('product_id'=>$same_rent['product_id'],'order_id'=>$same_rent['order_id'],'giver_id'=>$data['giver_id'],'recipient_id'=>$data['recipient_id'],'transaction_date_is_today'=>$data['transaction_date_is_today'],'no_schedule'=>$data['no_schedule'],'schedule_date'=>$data['schedule_date'],'transaction_date'=>$data['transaction_date'],'branch_id'=>$data['branch_id']);

            $this->parent_model='Rent';

            $rent_data = parent::set_insert_data_pre($rent_data,$same_rent);
            
            if (empty($this->schedule)) {
                if (empty($rent_data['branch_id']) or empty($data['same_rent_product_id'])) {
                    $rent_data['exists'] = $this->exists_rent_product($rent_data['recipient_id']);
                } else {
                    $rent_data['exists'] = $this->exists_rent_product($rent_data['recipient_id'], $rent_data['branch_id']);
                }
            }

            $id = $this->{$this->model}->insert($rent_data);

            $this->load->model('Order');
            $rent_data['order_content'] = $this->Order->get_content($rent_data['order_id']);
        
            if ($this->schedule) {
                $this->load->model('OrderTransferSchedule');
                $this->OrderTransferSchedule->insert(array('order_transfer_id' => $id, 'schedule_date' => $rent_data['schedule_date']));
            }
            
            $this->Rent->update(array('id' => $same_rent['id'], 'end_datetime' => $data['transfer_date']));

            $rent_data['rent_content']=$same_rent;
            $rent_data['order_id'] = $this->insert_new_rent($rent_data);
            
            if(!empty($data['branch_id'] and !empty($data['same_rent_product_id']))) {
                $rent_data['product_id'] = $data['same_rent_product_id'];
                $this->trans_rent_new_branch($id, $rent_data);
            }
        }

        if(!empty($data['same_rent_sw'])) {

            $this->load->model('RentSw');
            $same_rent_sw=$this->RentSw->get_content_by_order_id($data['same_rent_sw']);

            $rent_sw_data=array('product_id'=>$same_rent_sw['product_id'],'order_id'=>$same_rent_sw['order_id'],'giver_id'=>$data['giver_id'],'recipient_id'=>$data['recipient_id'],'transaction_date_is_today'=>$data['transaction_date_is_today'],'no_schedule'=>$data['no_schedule'],'schedule_date'=>$data['schedule_date'],'transaction_date'=>$data['transaction_date'],'branch_id'=>$data['branch_id']);

            $this->parent_model='RentSw';

            $rent_sw_data = parent::set_insert_data_pre($rent_sw_data,$same_rent_sw);
            $this->parent_model = 'RentSw';
            
            if (empty($this->schedule)) {
                if (empty($rent_sw_data['branch_id']) or empty($data['same_rent_sw_product_id'])) {
                    $rent_sw_data['exists'] = parent::exists_product($same_rent_sw['product_id'], $rent_sw_data['recipient_id']);
                } else {
                    $rent_sw_data['exists'] = parent::exists_product($data['same_rent_sw_product_id'], $rent_sw_data['recipient_id'], $rent_sw_data['branch_id']);
                }
            }

            $id = $this->{$this->model}->insert($rent_sw_data);

            $this->load->model('Order');
            $rent_sw_data['order_content'] = $this->Order->get_content($rent_sw_data['order_id']);
    
            if ($this->schedule) {
                $this->load->model('OrderTransferSchedule');
                $this->OrderTransferSchedule->insert(array('order_transfer_id' => $id, 'schedule_date' => $rent_sw_data['schedule_date']));
            } else {
                $this->Order->update(array('user_id' => $rent_sw_data['recipient_id'], 'transaction_date' => $this->today, 'id' => $rent_sw_data['order_content']['id']));
            }

            if (empty($rent_sw_data['exists'])) {  // 없을때
                $date_obj = new DateTime($data['transfer_date'], $this->timezone);
                $start_date = $date_obj->format('Y-m-d');
            } else { // 양수인이 해당 상품 있을때
                $date_obj = new DateTime($rent_sw_data['exists']['end_date'], $this->timezone);
                $date_obj->modify('+1 Days');
                $start_date = $date_obj->format('Y-m-d');
            }
    
            $start_date = $date_obj->format('Y-m-d');
            $give_count = $data['give_count'] - 1;
            $date_obj->modify('+' . $give_count . ' Days');
            $end_date = $date_obj->format('Y-m-d');
    
            $this->RentSw->update(array('id' => $same_rent_sw['id'], 'start_date' => $start_date, 'end_date' => $end_date));

            if(!empty($data['branch_id'] and !empty($data['same_rent_sw_product_id']))) {
                $rent_sw_data['product_id']=$data['same_rent_sw_product_id'];
                parent::trans_new_branch($id,$rent_sw_data);
            }
        }
        
        // 출입제어와 동기화
        $this->sync_access_controller($data['recipient_id']);
        $this->sync_access_controller($data['giver_id']);
    }

    protected function exists_rent_product($recipient_id, $branch_id=null)
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

        $this->load->model('Rent');
        $this->Rent->user_id = $recipient_id;
        $this->Rent->product_id = $product_id;
        $this->Rent->get_not_end_only = true;
        
        if(!empty($branch_id)) {
            $this->Rent->branch_id = $branch_id;
        }
        
        $orders = $this->Rent->get_index(1000, 0, 'end_date');

        if (!$orders['total']) {
            return false;
        }

        return $orders['list'][0];
    }


    protected function trans_rent_new_branch($id, array $data)
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

    protected function insert_new_rent(array $data)
    {
        $this->load->model('Order');
        $new_order_id = $this->Order->insert(array('user_id' => $data['recipient_id'], 'transaction_date' => $this->today));

        if (empty($data['exists'])) { // 없을때
            $start_date = $data['transfer_date'];
            $end_date = $data['rent_content']['end_date'];
            $product_id =  $data['rent_content']['product_id'];
        } else {
            $product_id =  $data['exists']['product_id'];
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

        $this->Rent->insert(array('facility_id' => $data['rent_content']['facility_id'], 'order_id' => $new_order_id, 'no' => $no, 'insert_quantity' => $insert_quantity, 'start_datetime' => $start_datetime, 'end_datetime' => $end_datetime));

        return $new_order_id;
    }

    protected function trans_new_branch($id, array $data)
    {
        if(!parent::trans_new_branch($id,$data)) {
            return false;
        }

        $this->load->model('Course');
        $course = $this->Course->get_content_by_product_id($data['product_id']);

        $this->load->model('Enroll');
        $enroll_id = $this->Enroll->get_id_by_order_id($data['order_id']);
        $this->Enroll->update(array('course_id' => $course['id'], 'id' => $enroll_id));

        $order_content = $this->Enroll->get_content_by_order_id($data['order_id']);        

        $this->load->model('EnrollTrainer');
        $enroll_trainer_count=$this->EnrollTrainer->get_count_by_parent_id($order_content['id']);

        if(!empty($enroll_trainer_count)) {
            $this->EnrollTrainer->delete_by_parent_id($order_content['id']);
        }
        
        $this->load->model('User');
        $this->User->no_branch=true;
        $user_content=$this->User->get_content($data['recipient_id']);

        if(!empty($user_content['trainer_id'])) {
            $this->EnrollTrainer->insert(array('enroll_id'=>$order_content['id'],'trainer_id'=>$user_content['trainer_id']));
        }

        return true;
    }

    protected function insert_complete_message($id)
    {
        if ($this->schedule) {
            return _('Successfully Schedule Transfer Enroll');
        } else {
            return _('Successfully Transfer Enroll');
        }
    }
}
