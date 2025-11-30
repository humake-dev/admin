<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order_extend.php';

class Enrolls extends Order_extend
{
    protected $product_model = 'Course';
    protected $model = 'Enroll';
    protected $permission_controller = 'enrolls';
    protected $script = 'enrolls/index.js';
    protected $p_relid = 'course_id';
    protected $use_index_content = true;
    protected $add_id = ADD_ENROLL;
    protected $edit_id = EDIT_ENROLL;
    protected $refund_id = REFUND_ENROLL;

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        if ($this->product_model) {
            $category = $this->get_category($category_id);

            if ($category['total']) {
                $this->{$this->model}->category_id = $category['current_id'];
            }
        }

        if ($this->input->get('user_id')) {
            $list = $this->{$this->model}->user_id = $this->input->get('user_id');
        }

        if (!$this->input->get('all')) {
            $this->Enroll->get_not_end_only = true;
        }
        $list = $this->{$this->model}->get_index($this->per_page, $this->page, 'start_date');
        $this->return_data['data'] = $list;

        $this->load->model('OrderTransfer');
        $enroll_transfer_list = $this->OrderTransfer->get_index($this->per_page, $this->page);

        if (isset($enroll['content'])) {
            $this->load->model('Account');
            $this->Account->order_id = $enroll['content']['order_id'];
            $this->Account->no_commission = true;
            $this->Account->no_branch_transfer = true;
            $this->return_data['data']['log'] = $this->Account->get_index(100, 0);
        } else {
            $this->return_data['data']['log']['total'] = 0;
        }

        $this->Enroll->get_end_only = true;
        $enroll_end_list = $this->Enroll->get_index(100, 0);

        if ($this->use_index_content) {
            $this->return_data['data']['content'] = $this->get_list_view_data($list);
        }

        if (isset($category)) {
            $this->return_data['data']['category'] = $category;
        }

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->return_data['data']['enroll'] = $list;
        $this->return_data['data']['enroll']['content'] = $this->return_data['data']['content'];
        $this->return_data['data']['end_list'] = $enroll_end_list;
        $this->return_data['data']['transfer_list'] = $enroll_transfer_list;
    }

    protected function set_add_form_data()
    {
        $this->set_form_data();
    }

    protected function set_edit_form_data(array $content)
    {
        $this->set_form_data($content['id']);
    }

    protected function set_form_data($id = null)
    {
        if (empty($id)) {
            if ($this->input->get('after')) {
                $this->load->model($this->model);
                $content = $this->after_same_insert($this->input->get('after'));

                $user_id = $content['user_id'];
            }

            if ($this->input->get_post('user_id')) {
                $user_id = $this->input->get_post('user_id');
            }

            if (!empty($user_id)) {
                $this->load->model('User');
                $user_content = $this->User->get_content($user_id);
                $user_id = $user_content['id'];
            }

            if ($this->input->get_post('course_id')) {
                $this->load->model('Course');
                $product_content = $this->Course->get_content($this->input->get_post('course_id'));
            }
        } else {
            $content = $this->get_view_content_data($id);

            $this->load->model('Course');
            $product_content = $this->Course->get_content($content['course_id']);

            $this->load->model('User');
            $user_content = $this->User->get_content($content['user_id']);
        }

        $this->load->model('productCategory');
        $this->productCategory->type = 'course';
        $courseCategories = $this->productCategory->get_index(100, 0);

        $this->load->model('Course');
        $this->Course->status = 1;
        $course = $this->Course->get_index(100, 0,'c.order_no',false);

        $this->return_data['data']['course_category'] = $courseCategories;
        $this->return_data['data']['course'] = $course;
        $this->return_data['data']['trainer'] = $this->get_admin_list('trainer');

        if (!empty($product_content)) {
            $this->return_data['data']['product_content'] = $product_content;
        }

        if (!empty($content)) {
            $this->return_data['data']['content'] = $content;
        }

        if (!empty($user_content)) {
            // 왼쪽 수강정보 리스트
            $this->load->model($this->model);
            $this->{$this->model}->user_id = $user_content['id'];
            $this->{$this->model}->get_not_end_only = true;
            $this->return_data['data']['enroll_list'] = $this->{$this->model}->get_index(5, 0, 'start_date');

            $this->load->model('Rent');
            $this->Rent->user_id = $user_content['id'];
            $this->Rent->get_not_end_only = true;
            $this->return_data['data']['rent_list'] = $this->Rent->get_index(5, 0, 'start_date');
        }

        // 수강시 판매 제품
        $this->load->model('ProductRelation');
        $this->ProductRelation->product_relation_type_id = SUB_ORDER_ID;
        $this->ProductRelation->display_type = 'enroll';

        if (!empty($user_content['gender'])) {
            $this->ProductRelation->gender = $user_content['gender'];
        }

        $product_option_list = $this->ProductRelation->get_index(10, 0);

        $pol_category_list = array();
        if ($product_option_list['total']) {
            foreach ($product_option_list['list'] as $pol) {
                $pol_category_list[] = $pol['rel_product_type'];
            }

            $this->return_data['data']['product_option_category'] = array_unique($pol_category_list);
        } else {
            $this->return_data['data']['product_option_category'] = false;
        }
        $this->return_data['data']['product_option'] = $product_option_list;

        if (empty($user_content)) {
            if (!empty($user_id)) {
                $this->return_data['data']['user_id'] = $user_id;
            }
        } else {
            $this->return_data['data']['user_content'] = $user_content;
            $this->return_data['data']['user_id'] = $user_content['id'];
        }

        $this->script = 'enrolls/add.js';
    }

    private function set_data($data)
    {
        $data = parent::set_insert_data($data);

        if (empty($data['trainer'])) {
            $data['trainer_id'] = null;
        } else {
            $data['trainer_id'] = $data['trainer'];
            unset($data['trainer']);
        }

        if (empty($data['have_date_is_today'])) {
            $data['have_datetime'] = $data['custom_have_date'] . ' 00:00:01';
        } else {
            $data['have_datetime'] = $this->now;
        }

        unset($data['custom_have_date']);

        return $data;
    }

    protected function set_insert_data($data)
    {
        $data = $this->set_data($data);

        if (empty($data['start_date'])) {
            $data['start_date'] = $this->today;
        }

        if (empty($data['end_date'])) {
            $start_datetime_obj = new DateTime($data['start_date'], $this->timezone);
            $start_datetime_obj->modify('last day of this month');
            $data['end_date'] = $start_datetime_obj->format('Y-m-d');
        }

        switch ($data['product']['lesson_type']) {
            case 1:
                $data['quantity'] = 0;
                break;
            default:
                $data['quantity'] = $data['product']['lesson_quantity'] * $data['insert_quantity'];
                break;
        }

        switch ($data['product']['lesson_period_unit']) {
            case 'D' :
                $data['type']='day';
                break;
            case 'W' :
                $data['type']='week';
                break;
            default: 
                $data['type']='month';
        }

        if (!empty($data['commission_default']) and in_array($data['product']['lesson_type'], array(4, 5)) and !empty($data['trainer_id'])) {
            $this->load->model('Employee');
            $employee = $this->Employee->get_content($data['trainer_id']);

            $data['commission_default'] = false;
            $data['commission'] = ($data['price'] / $data['insert_quantity']) * ($employee['commission_rate'] / 100);
        }

        return $data;
    }

    protected function after_insert_data($id, $data)
    {
        if (!empty($data['trainer_id'])) {
            $this->load->model('EnrollTrainer');
            $this->EnrollTrainer->insert(array('enroll_id' => $id, 'trainer_id' => $data['trainer_id']));
        }

        $this->load->model('Account');
        $data['account_category_id'] = $this->add_id;
        $this->Account->insert($data);

        $insert_order = false;

        if (!empty($data['order'])) {
            foreach ($data['order'] as $index => $order) {
                if (key_exists('check', $order)) { // 체크가 있으면
                    $insert_order = true;  // 추가 삽입
                } else {
                    unset($data['order'][$index]); // 배열 제거
                }
            }
        }

        if ($insert_order) {    // 추가 삽입이 있으면
            $order = $this->set_order_insert_data(array('list' => $data['order'], 'user_id' => $data['user_id'], 'transaction_date' => $data['transaction_date']));

            $this->load->model('Order');
            $this->load->model('OrderProduct');
            $this->load->model('RentSw');
            $this->load->model('Rent');

            foreach ($order['order_product'] as $index => $value) {
                $cash=filter_var($value['cash'], FILTER_SANITIZE_NUMBER_INT);
                $credit=filter_var($value['credit'], FILTER_SANITIZE_NUMBER_INT);

                if(empty($cash)) {
                    $cash=0;
                }

                if(empty($credit)) {
                    $credit=0;
                }

                $order_id = $this->Order->insert(array('user_id' => $data['user_id'], 'cash' => $cash, 'credit' => $credit, 'transaction_date' => $data['transaction_date'], 'original_price' => $value['total_price'], 'price' => $cash + $credit, 'payment' => $cash + $credit));
                $account_category_id = ADD_ORDER;

                if ($value['type'] == 'rent_sw') {
                    echo $this->RentSw->insert(array('order_id' => $order_id, 'start_date' => $data['start_date'], 'end_date' => $data['end_date']));
                }

                if ($value['type'] == 'rent') {
                    $no=0;
                    
                    if(!empty($data['re_order_no'])) {
                        $this->load->model('Rent');
                        $this->Rent->display_only=true;
                        $this->Rent->product_id=$value['product_id'];
                        $this->Rent->no=$data['re_order_no'];
                        $rents=$this->Rent->get_index(10000);

                        $exists=false;
                        if(!empty($rents['total'])) {
                            $check_start_date_obj = new DateTime($data['start_date'], $this->timezone);
                            $check_end_date_obj = new DateTime($data['end_date'], $this->timezone);

                            foreach($rents['list'] as $rent) {
                                $start_date_obj = new DateTime($rent['start_datetime'], $this->timezone);
                                $end_date_obj = new DateTime($rent['end_datetime'], $this->timezone);

                                if (($check_start_date_obj < $end_date_obj) and ($check_end_date_obj > $start_date_obj)) {
                                    $exists=true;
                                }
                            }
                        }

                        if(empty($exists)) {
                            $no=$data['re_order_no'];
                        }
                    }

                    $rent_insert_quantity=0;
                    if ($data['type']=='month') {
                        $rent_insert_quantity=$data['insert_quantity'];
                    }

                    $this->load->model('Facility');
                    $facility_content = $this->Facility->get_content_by_product_id($value['product_id']);
                    $this->Rent->insert(array('no'=>$no,'facility_id' => $facility_content['id'], 'order_id' => $order_id, 'start_datetime' => $data['start_date'], 'end_datetime' => $data['end_date'], 'insert_quantity' => $rent_insert_quantity));
                    $account_category_id = ADD_RENT;
                }

                $value['order_id'] = $order_id;
                $this->OrderProduct->insert($value);

                $this->Account->insert(array('account_category_id' => $account_category_id, 'user_id' => $data['user_id'], 'cash' => $value['cash'], 'credit' => $value['credit'], 'order_id' => $order_id, 'transaction_date' => $data['transaction_date'], 'product_id' => $value['product_id']));
            }
        }

        if ($this->session->userdata('role_id') < 3) {
            $this->load->model('EnrollCommission');
            if (empty($data['commission_default']) and isset($data['commission'])) {
                $this->EnrollCommission->insert(array('enroll_id' => $id, 'commission' => $data['commission']));
            }
        }

        $this->load->model('EnrollPt');
        if (!empty($data['pt_serial'])) {
            $this->EnrollPt->insert(array('enroll_id' => $id, 'serial' => $data['pt_serial']));
        }

        $content = $this->get_view_content_data($id);
        $this->sync_access_data($content);
    }

    protected function insert_complete_message($id)
    {
        return _('Successfully Enroll');
    }

    protected function add_redirect_path($id)
    {
        return '/home/enrolls/' . $this->input->post('user_id');
    }

    protected function set_update_data($id, $data)
    {
        $content = $this->get_view_content_data($id);

        $data['id'] = $id;
        $data['user_id'] = $content['user_id'];
        $data = $this->set_data($data);

        switch ($data['product']['lesson_type']) {
            case 1:
                $data['quantity'] = 0;
                break;
            default:

                if ($this->session->userdata('role_id') < 3) {
                    if (isset($data['quantity'])) {
                        $data['quantity'] = $data['quantity'] + $data['use_quantity'];
                    } else {
                        $data['quantity'] = $data['product']['lesson_quantity'] * $data['insert_quantity'];
                    }
                } else {
                    $data['insert_quantity'] = $content['insert_quantity'];
                    $data['quantity'] = $content['quantity'];
                }
                break;
        }

        $df_data = array();
        $refference_a = array('trainer' => 'trainer_id');
        $remove_a = array('INAMTValue', 'AMTValue', 'payment_complete', 'no_discount', 'payment_method', 'mix_credit', 'mix_cash', 'is_today', 'commission_default', 'payment_type', 'product_category_name', 'product_name', 'product', 'change_content', 'select_payment', 'order', 'use_default_price', 'use_auto_extend');

        foreach ($data as $key => $value) {
            if (in_array($key, $remove_a)) {
                continue;
            }

            if (!empty($refference_a[$key])) {
                $key = $refference_a[$key];
            }

            if(isset($content[$key])) {
                if ($value != $content[$key]) {
                    $df_data[] = array('field' => $key, 'origin' => $content[$key], 'change' => $value);
                }
            }
        }

        $data['change'] = array('field' => $df_data, 'content' => $data['change_content']);

        return $data;
    }

    protected function after_update_data($id, $data)
    {
        $data['account_category_id'] = $this->edit_id;
        $content=parent::after_update_data($id, $data);

        if ($this->session->userdata('role_id') < 6) {
            $this->load->model('EnrollTrainer');
            $count = $this->EnrollTrainer->get_count_by_parent_id($id);

            if (empty($data['trainer_id'])) {
                if ($count) {
                    $this->EnrollTrainer->delete_by_parent_id($id);
                }
            } else {
                if ($count) {
                    $this->EnrollTrainer->update_by_parent_id(array('parent_id' => $id, 'trainer_id' => $data['trainer_id']));
                } else {
                    $this->EnrollTrainer->insert(array('enroll_id' => $id, 'trainer_id' => $data['trainer_id']));
                }
            }
        }

if ($this->session->userdata('role_id') < 3) {
    $this->load->model('EnrollCommission');
    if (empty($data['commission_default']) and isset($data['commission'])) {
        $this->EnrollCommission->insert(array('enroll_id' => $id, 'commission' => $data['commission']));
    } else {
        if ($this->EnrollCommission->get_count_by_parent_id($id)) {
            $this->EnrollCommission->delete_by_parent_id($id);
        }
    }
}

if ($this->session->userdata('role_id') < 4) {
    $this->load->model('EnrollPt');
    if (empty($data['pt_serial'])) {
        if ($this->EnrollPt->get_count_by_parent_id($id)) {
            $this->EnrollPt->delete_by_parent_id($id);
        }
    } else {
        $this->EnrollPt->insert(array('enroll_id' => $id, 'serial' => $data['pt_serial']));
    }
}


        $this->sync_access_data($content);        
    }

    public function view($id = null)
    {
        if (empty($id)) {
            show_404();
        }

        if ($content = $this->get_view_data($id)) {
            $this->return_data['data'] = ['content' => $content];

            $this->load->model('Rent');
            $this->Rent->user_id=$content['user_id'];
            $this->Rent->start_date=$content['start_date'];
            $this->Rent->end_date=$content['end_date'];
            if($this->Rent->get_count()) {
                $rents=$this->Rent->get_index();
                $rent=$rents['list'][0];
                $this->return_data['data']['re_order_no']=$rent['no'];
            }
        }

        $this->render_view_format();
    }

    /*
    public function transfer($id)
    {
          coinfig/router.php  enroll/transfer = enrolll_transfer/add
        = controllers/Enroll_transfers/add
    }

    public function stop($id)
    {
          coinfig/router.php  enroll/stop = enrolll_stop/add
        = controllers/Enroll_stops/add
    }

    public function resume($id)
    {
          coinfig/router.php  enroll/resume = enrolll_stop/resume
        = controllers/Enroll_stops/resume
    }
    */


    public function recover($id)
    {
        $content = $this->get_view_content_data($id);

        $this->load->model('OrderEnd');
        $order_end_content=$this->OrderEnd->get_content_by_parent_id($content['order_id']);

        if(!empty($order_end_content['origin_end_date'])) {
            $content['origin_end_date']= $order_end_content['origin_end_date'];
        }

        $this->set_recover_form_validation($content);

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
                $this->return_data['data']['content'] = $content;
                $this->script = 'orders/recover.js';

                $this->render_format();
            } else {
                echo json_encode(array('result' => 'error', 'message' => validation_errors()));
            }
        } else {
            $data = $this->input->post(null, true);
            $result = true;
            
            $this->load->model('OrderEnd');
            $this->OrderEnd->delete_by_parent_id($content['order_id']);

            $this->load->model('Enroll');
            $this->Enroll->update(array('end_date'=>$data['change_end_date'],'id'=>$content['id']));

            $this->load->model('Account');
            $this->Account->account_category_id=$this->refund_id;
            $this->Account->order_id = $content['order_id'];
            $this->Account->user_id = $content['user_id'];
            $accounts=$this->Account->get_index();

            if(!empty($accounts['total'])) {
                $account=$accounts['list'][0];
                $this->Account->delete($account['id']);
            }
            
            if ($result) {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'success', 'message' => $this->recover_complete_message(), 'redirect_path' => $this->recover_complete_message()));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'success', 'message' => $this->recover_complete_message()));
                    redirect($this->end_redirect_path($content));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'error', 'message' => _('End Fail')));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('End Fail')));
                    redirect($this->end_redirect_path($content));
                }
            }
        }
    }

    protected function set_recover_form_validation(array $content)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('change_end_date', _('Change End Date'), 'required|callback_valid_date');
        $this->set_message();
    }

    protected function edit_redirect_path($id)
    {
        $content = $this->get_view_content_data($id);

        return '/home/enrolls/' . $content['user_id'];
    }

    protected function delete_complete_message(array $content)
    {
        return _('Successfully Delete Enrollment');
    }

    protected function recover_complete_message()
    {
        return _('Successfully Recover Enrollment');
    }

    protected function set_end_form_validation(array $content)
    {
        $this->load->library('form_validation');

        if ($content['lesson_type'] == 4) {
            $this->form_validation->set_rules('end_date', _('Change End Date'), 'callback_valid_date');
        } else {
            if (new DateTime($content['start_date'], $this->timezone) <= new DateTime($this->today, $this->timezone)) {
                $this->form_validation->set_rules('end_date', _('Change End Date'), 'callback_valid_date|callback_valid_date_after[' . $content['start_date'] . ']|callback_valid_date_before[' . $content['end_date'] . ']');
            } else {
                $this->form_validation->set_rules('end_date', _('Change End Date'), 'callback_valid_date');
            }
        }
        $this->set_message();
    }

    protected function set_form_validation($id = null)
    {
        parent::set_form_validation($id);

        if ($this->router->fetch_method() == 'add') {
            $this->form_validation->set_rules('user_id', _('User'), 'required|integer');
        } else {
            $this->form_validation->set_rules('insert_quantity', _('Insert Quantity'), 'integer');

            if($this->session->userdata('role_id')!=1) {
                $this->form_validation->set_rules('change_content', _('Change Content'), 'required|trim');
            }
        }

        $this->form_validation->set_rules('custom_transaction_date', _('Transaction Date'), 'callback_valid_date');
        $this->form_validation->set_rules('custom_have_date', _('Have Datetime'), 'callback_valid_date');

        $this->form_validation->set_rules('course_id', _('Course'), 'required|integer');
        $this->form_validation->set_rules('trainer_id', _('Trainer'), 'integer');
        $this->form_validation->set_rules('quantity', _('Quantity'), 'integer');
        $this->form_validation->set_rules('use_quantity', _('Use Quantity'), 'integer');
        $this->form_validation->set_rules('pt_serial', _('PT Serial'), 'integer|callback_valid_serial['.$id.']');

        $this->form_validation->set_rules('start_date', _('Start Date'), 'callback_valid_date');
        if ($this->input->post('end_date')) {
            $this->form_validation->set_rules('end_date', _('End Date'), 'callback_valid_date|callback_valid_date_after[' . $this->input->post('start_date') . ']');
        }
        $this->form_validation->set_rules('commission', _('Commission'), 'integer');
        $this->form_validation->set_rules('re-enroll', _('Re Enroll'), 'in_list[1]');
    }

    public function valid_serial($serial,$enroll_id)
    {
        if(empty($serial)) {
            return true;
        }

        $this->load->model('EnrollPt');
        if ($this->EnrollPt->exists_unique_serial($serial,$enroll_id)) {
            return false;
        }

        return true;
    }

    public function export_excel()
    {
        $this->per_page = 10000;
        $this->no_set_page = true;
        $this->index_data();
        $list = $this->return_data['data']['enroll'];

        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator('작성자')
            ->setLastModifiedBy('최종수정자')
            ->setTitle('자격증시험응시리스트')
            ->setSubject('자격증시험응시리스트')
            ->setDescription('자격증시험응시리스트')
            ->setKeywords('자격증 시험')
            ->setCategory('License');

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', _('Transaction Date'))
            ->setCellValue('B1', _('Status'))
            ->setCellValue('C1', _('Course'))
            ->setCellValue('D1', _('Enroll Trainer'))
            ->setCellValue('E1', _('Course Day'))
            ->setCellValue('F1', _('Lesson Type'))
            ->setCellValue('G1', _('Start Date'))
            ->setCellValue('H1', _('End Date'))
            ->setCellValue('I1', _('Remain Day'))
            ->setCellValue('J1', _('Remain Count'))
            ->setCellValue('K1', _('Original Price'))
            ->setCellValue('L1', _('Discount'))
            ->setCellValue('M1', _('DC Price'))
            ->setCellValue('N1', _('Price'))
            ->setCellValue('O1', _('Payment'))
            ->setCellValue('P1', _('Memo'));

        if ($list['total']) {
            foreach ($list['list'] as $index => $value) {
                $memo = '-';
                if (!empty($value['content'])) {
                    $memo = $value['content'];
                }

                $remain_date = '-';
                $remain_count = '-';

                $D2 = new DateTime($value['end_date'], $this->timezone);
                if ($D2 > new DateTime($this->max_date, $this->timezone)) {
                    $remain_date = '-';
                    $value['end_date'] = '-';
                } else {
                    $D1 = new DateTime('now', $this->timezone);
                    $remain_date = ($D1 < $D2) ? date_diff($D1, $D2)->days . _('Day') : '만기';
                }

                switch ($value['lesson_type']) {
                    case 1: // 기간제
                        break;
                    case 2: // 횟수제
                    case 4: // PT
                    case 5: // GX
                        $remain_count = (int)preg_replace('/[^0-9]/', '', $value['lesson_quantity'] * $value['quantity']) . _('Count Time'); // 단위수량 X 구입갯수
                        break;
                    case 3: // 쿠폰제
                        $remain_count = (int)preg_replace('/[^0-9]/', '', $value['lesson_quantity'] * $value['quantity']) . _('Count'); // 단위수량 X 구입갯수
                        break;
                }

                $start_date = $value['start_date'];
                $end_date = $value['end_date'];

                if (!empty($value['change_start_date'])) {
                    $start_date = $value['change_start_date'];
                }

                if (!empty($value['change_end_date'])) {
                    $end_date = $value['change_end_date'];
                }

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($index + 2), $value['transaction_date'])
                    ->setCellValue('B' . ($index + 2), $value['enable'])
                    ->setCellValue('C' . ($index + 2), $value['product_category_name'] . ' / ' . $value['product_name'])
                    ->setCellValue('D' . ($index + 2), $value['trainer_name'])
                    ->setCellValue('E' . ($index + 2), dowtostr($value['dow']))
                    ->setCellValue('F' . ($index + 2), get_lesson_type($value['lesson_type']))
                    ->setCellValue('G' . ($index + 2), $start_date)
                    ->setCellValue('H' . ($index + 2), $end_date)
                    ->setCellValue('I' . ($index + 2), $remain_date)
                    ->setCellValue('J' . ($index + 2), $remain_count)
                    ->setCellValue('K' . ($index + 2), number_format($value['original_price']))
                    ->setCellValue('L' . ($index + 2), number_format($value['original_price'] * $value['dc_rate'] / 100))
                    ->setCellValue('M' . ($index + 2), $value['dc_price'])
                    ->setCellValue('N' . ($index + 2), number_format($value['price']))
                    ->setCellValue('O' . ($index + 2), number_format($value['payment']))
                    ->setCellValue('P' . ($index + 2), $memo);
            }
        }

        $filename = iconv('UTF-8', 'EUC-KR', '예약목록');

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $objWriter->save('php://output');
    }

    protected function setting_pagination(array $config)
    {
        $this->load->library('pagination');

        if (empty($config['per_page'])) {
            $config['per_page'] = $this->per_page;
        }

        if ($this->router->fetch_method() == 'edit') {
            $config['base_url'] = base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $config['ep_id'];
        } else {
            $config['base_url'] = base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method();
        }
        $config['page_query_string'] = true;
        $config['use_page_numbers'] = true;
        $config['query_string_segment'] = 'page';

        $query_string = $this->input->get();
        if (isset($query_string['page'])) {
            unset($query_string['page']);
        }

        if (count($query_string) > 0) {
            $config['suffix'] = '&' . http_build_query($query_string, '', '&');
            $config['first_url'] = $config['base_url'] . '?' . http_build_query($query_string, '', '&');
        }

        $config['num_links'] = 2;
        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = _('First');
        $config['first_tag_open'] = '<li class="prev page-item">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = _('Last');
        $config['last_tag_open'] = '<li class="next page-item">';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = false;
        $config['prev_link'] = false;

        $config['cur_tag_open'] = '<li class="active page-item"><a href="" class="page-link">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $config['attributes'] = array('class' => 'page-link');
        $this->pagination->initialize($config);
    }

    protected function get_error_messages()
    {
        $message = parent::get_error_messages();
        $message['valid_serial'] = _('The %s field must contain a unique value.');

        return $message;
    }
}
