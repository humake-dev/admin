<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Ac_sync.php';

class Order_transfers extends SL_Controller
{
    use Ac_sync;

    protected $parent_model = 'Order';
    protected $model = 'OrderTransfer';
    protected $permission_controller = 'orders';
    protected $script = 'order-transfers/add.js';
    protected $default_view_directory = 'order_transfers';
    protected $default_view_file = 'add.php';
    protected $schedule = false;
    protected $type = 'order';
    protected $transfer_id = TRANSFER_ORDER;

    protected function permission_check()
    {
        parent::permission_check();

        if ($this->session->userdata('role_id') > 3) {
            show_error('You do not have access to this section');
            exit;
        }
    }

    protected function set_add_form_data()
    {
        $parent_id = $this->uri->segment(3);

        $this->load->model($this->parent_model);
        $content = $this->{$this->parent_model}->get_content($parent_id);

        $this->return_data['data']['content'] = $content;

        $this->load->model('OrderTransferSchedule');
        $this->OrderTransferSchedule->order_id = $content['order_id'];
        $transfer_schedule = $this->OrderTransferSchedule->get_index(1, 0);

        $this->load->model('Branch');
        $this->Branch->not_branch_id = [1,$this->session->userdata('branch_id')];

        $branch_ids=$this->get_branch_ids();
        $this->Branch->id = $branch_ids;
        $branch_list = $this->Branch->get_index(1000, 0);

        if (!empty($transfer_schedule['total'])) {
            $this->return_data['data']['transfer_schedule_content'] = $transfer_schedule['list'][0];

            $this->default_view_file='delete';
        }

        $this->return_data['data']['branch_list'] = $branch_list;
        $this->return_data['data']['content'] = $content;
        $this->return_data['data']['type'] = $this->type;

        return $content;
    }

    protected function set_insert_data_pre($data,$content) {
        $data['origin_start_date'] = $content['start_date'];
        $data['origin_end_date'] = $content['end_date'];

        $start_date_obj = new DateTime($content['start_date'], $this->timezone);
        $end_date_obj = new DateTime($content['end_date'], $this->timezone);
        $today_obj = new DateTime($this->today, $this->timezone);

        if (empty($data['no_schedule'])) { // 지금적용이 체크 되지 않고
            $schedule_date_obj = new DateTime($data['schedule_date'], $this->timezone);
            $interval = $schedule_date_obj->diff($today_obj);

            if ($interval->format('%R') == '-') { // 날짜가 더 크면
                $this->schedule = true;  // 예약
                $data['enable'] = 0;  // 예약인 경우 비 활성화 => 예약 처리후(cron/order_transfer.php) 활성화됨
            }

            $transfer_date_obj = $schedule_date_obj;
        } else {
            $transfer_date_obj = $today_obj;
        }

        $data['transfer_date'] = $transfer_date_obj->format('Y-m-d');        
        
        if ($end_date_obj <= $today_obj) {
            $give_count = 0;
        } else {
            if ($start_date_obj > $today_obj) {
                $transfer_date_obj = $start_date_obj;
            }

            $interval = $transfer_date_obj->diff($end_date_obj);
            $give_count = $interval->format('%a');
            $give_count++;
        }

        $data['give_count'] = $give_count;    // 양도 날짜수,수량        

        if(empty($data['transaction_date'])) {
            if(empty($data['transaction_date_is_today']) and !empty($data['custom_transaction_date'])) {
                $data['transaction_date']  = $data['custom_transaction_date'].' 00:00:01';
            } else {
                $data['transaction_date']  = $this->today;
            }
        }

        unset($data['custom_transaction_date']);        

        $data['order_id'] = $content['order_id'];
        $data['giver_id'] = $content['user_id'];

        if (!empty($content['insert_quantity'])) {
            $data['origin_quantity'] = $content['insert_quantity'];
        }

        return $data;
    }

    protected function set_insert_data($data)
    {
        if(empty($data['sub_content'])) {
            $parent_id = $this->uri->segment(3);
            $this->load->model($this->parent_model);
            $content = $this->{$this->parent_model}->get_content($parent_id);
        } else {
            $content=$data['sub_content'];
        }

        $data=$this->set_insert_data_pre($data,$content);
        
        if(empty($data['content'])) {
            if (empty($data['branch_id']) or empty($data['product_id'])) {
                $data['exists'] = $this->check_exists_product($content['product_id'], $data['recipient_id']);
            } else {
                $data['exists'] = $this->check_exists_product($data['product_id'], $data['recipient_id'], $data['branch_id']);
            }
        }

        return $data;
    }

    protected function check_exists_product($product_id, $recipient_id, $branch_id=null)
    {
        if (!empty($this->schedule)) {
            return false;
        }
        
        return $this->exists_product($product_id, $recipient_id, $branch_id);
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
            $this->Order->update(array('user_id' => $data['recipient_id'], 'transaction_date' => $this->today, 'id' => $data['order_content']['id']));
        }

        $data  = $this->product_process($data);
        
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

    protected function insert_account(array $account_data)
    {
        $this->load->model('Account');
        return $this->Account->insert($account_data);
    }

    protected function trans_new_branch($id, array $data)
    {
        if (empty($data['branch_id']) or empty($data['product_id'])) {
            return false;
        }

        $this->load->model('Order');
        $this->Order->update(array('branch_id' => $data['branch_id'], 'id' => $data['order_id']));

        $this->load->model('OrderProduct');
        $this->OrderProduct->order_id = $data['order_id'];
        $orderProductList = $this->OrderProduct->get_index();

        $orderProductContent = $orderProductList['list'][0];

        $this->OrderProduct->update(array('product_id' => $data['product_id'], 'id' => $orderProductContent['id']));

        $this->load->model('User');
        $giver_content = $this->User->get_content($data['giver_id']);

        $add_content = sprintf(_('Order Transfer From Branch %s Person %s'), $this->session->userdata('branch_name'), $giver_content['name']);

        $this->load->model('OrderContent');
        $this->OrderContent->order_id = $data['order_id'];
        $order_content_list = $this->OrderContent->get_index();

        if (empty($order_content_list['total'])) {
            $this->OrderContent->insert(array('order_id' => $data['order_id'], 'content' => $add_content));
        } else {
            $order_content_content = $order_content_list['list'][0];
            $this->OrderContent->update(array('id' => $order_content_content['id'], 'content' => $order_content_content['content'] . "\n\n" . $add_content));
        }
        
        $this->load->model('OrderTransferOtherBranch');
        return $this->OrderTransferOtherBranch->insert(array('order_transfer_id' => $id, 'origin_branch_id' => $data['order_content']['branch_id'], 'origin_product_id' => $data['order_content']['product_id'], 'transfer_branch_id' => $data['branch_id'], 'transfer_product_id' => $data['product_id']));
    }

    // 양도시 받는사람이 이미 있는 상품인지, 아닌지 확인
    protected function exists_product($product_id, $recipient_id, $branch_id=null)
    {
        $this->load->model($this->parent_model);
        $this->{$this->parent_model}->user_id = $recipient_id;
        $this->{$this->parent_model}->product_id = $product_id;
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

    // 상품날짜 처리
    protected function product_process(array $data)
    {
        return $data;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('recipient_id', _('Recipient'), 'required|integer');
        $this->form_validation->set_rules('content', _('Transfer Memo'), 'trim');
        $this->form_validation->set_rules('branch_id', _('Branch'), 'integer|callback_validate_other_branch_branch_id');
        $this->form_validation->set_rules('product_id', _('Product'), 'integer|callback_validate_other_branch_product_id');
        $this->form_validation->set_rules('commission', _('Trans Commission'), 'integer');
        $this->form_validation->set_rules('transaction_date', _('Transaction Date'), 'callback_valid_date');

        if (!$this->input->post('no_schedule')) {
            $this->form_validation->set_rules('schedule_date', _('Schedule Date'), 'required|callback_valid_date');
        }
    }

    public function validate_other_branch_branch_id($branch_id = null)
    {
        if (empty($branch_id)) {
            return true;
        }

        if ($this->input->post('product_id')) {
            return true;
        }

        return false;
    }

    public function validate_other_branch_product_id($product_id = null)
    {
        if (empty($product_id)) {
            return true;
        }

        if ($this->input->post('branch_id')) {
            return true;
        }

        return false;
    }
}
