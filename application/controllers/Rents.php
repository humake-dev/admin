<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order_extend.php';

class Rents extends Order_extend
{
    protected $product_model = 'Facility';
    protected $model = 'Rent';
    protected $permission_controller = 'rents';
    protected $script = 'rents/index.js';
    protected $p_relid = 'facility_id';
    protected $category_id_name = 'facility_id';
    protected $price_on = false;
    protected $add_id = ADD_RENT;
    protected $edit_id = EDIT_RENT;
    protected $refund_id = REFUND_RENT;

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $category = $this->get_category($category_id);

        if ($category['total']) {
            $this->{$this->model}->facility_id = $category['current_id'];
        } else {
            $this->return_data['data']['category']['total'] = 0;
            $this->return_data['data']['rent']['total'] = 0;
            $this->return_data['data']['facility_breakdown']['total'] = 0;

            return false;
        }

        $this->{$this->model}->display_only = true;
        $rent_list = $this->{$this->model}->get_index(10000, 0, 'id', false);

        if ($this->input->get('id')) {
            $rent_content = $this->{$this->model}->get_content($this->input->get('id'));
            $no = $rent_content['no'];
        }

        if ($this->input->get('no')) {
            $no = $this->input->get('no');

            if ($rent_list['total']) {
                foreach ($rent_list['list'] as $value) {
                    if ($value['no'] == $no and $value['start_date'] <= $this->today) {
                        $aside_content = $value;
                    }
                }
            }
        }

        $this->return_data['data'] = $rent_list;

        if (isset($rent_content)) {
            $this->return_data['data']['content'] = $rent_content;
        } else {
            if (isset($aside_content)) {
                $this->return_data['data']['content'] = $aside_content;
            } else {
                $this->return_data['data']['content'] = false;
            }
        }

        if (isset($no)) {
            $this->return_data['data']['no'] = $no;
        }

        $this->load->model('FacilityBreakdown');
        $this->FacilityBreakdown->facility_id = $category['content']['id'];
        $this->return_data['data']['facility_breakdown'] = $this->FacilityBreakdown->get_index($category['content']['quantity'], 0);
        $this->return_data['data']['category'] = $category;
    }

    protected function set_add_form_data()
    {
        $this->set_form_data();
        $this->layout->add_js('sweetalert2.min.js');
        $this->script = 'rents/add.js';
    }

    protected function set_edit_form_data(array $content)
    {
        $this->set_form_data($content['id']);
        $this->layout->add_js('sweetalert2.min.js');
        $this->script = 'rents/add.js';
    }

    protected function set_form_data($id = null)
    {
        $this->load->model('User');

        $start_datetime = null;
        $end_datetime = null;

        if (empty($id)) {
            if ($this->input->get('after')) {
                $content = $this->after_same_insert($this->input->get('after'));

                $user_id = $content['user_id'];
            }

            if ($this->input->get_post('user_id')) {
                $user_id = $this->input->get_post('user_id');
            }

            if (empty($user_id)) {
                if ($this->input->get_post('card_no')) {
                    $user_content = $this->User->get_content_by_card_no($this->input->get_post('card_no'), $this->session->userdata('branch_id'));
                }

                //throw new Exception('Error Processing Request', 1);
            } else {
                $user_content = $this->User->get_content($user_id);
            }
        } else {
            $content = $this->get_view_content_data($id);

            $user_content = $this->User->get_content($content['user_id']);
        }

        $this->load->model('Facility');
        $facility_list = $this->Facility->get_index(100, 0);
        $this->return_data['data']['facility'] = $facility_list;

        if ($this->input->get_post('facility_id')) {
            $facility = $this->Facility->get_content($this->input->get_post('facility_id'));
            $facility_id = $facility['id'];
        } else {
            if (isset($content)) {
                $facility_id = $content['facility_id'];
            } else {
                $facility_id = $facility_list['list'][0]['id'];
            }
        }

        if (!empty($content)) {
            $start_datetime = $content['start_datetime'];
            $end_datetime = $content['end_datetime'];
        }

        if (!empty($this->input->get_post('start_date')) and !empty($this->input->get_post('end_date'))) {
            $start_datetime = $this->input->get_post('start_date') . ' 00:00:01';
            $end_datetime = $this->input->get_post('end_date') . ' 23:59:59';
        }

        $f_facility = $this->get_available_list($facility_id, $start_datetime, $end_datetime);
        $facility_content = $f_facility['content'];

        // 대여시 판매 제품
        $this->load->model('ProductRelation');
        $this->ProductRelation->display_type = 'rent';
        $this->ProductRelation->rel_product_id = $facility_content['product_id'];

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
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
        $this->return_data['data']['type'] = 'single';
        $this->return_data['data']['facility_available_no'] = $f_facility;
        $this->return_data['data']['facility']['content'] = $facility_content;
        $this->return_data['data']['product_price'] = $facility_content['price'];

        if (isset($content)) {
            $this->return_data['data']['content'] = $content;
        }

        if (!empty($user_content)) {
            // 왼쪽 대여정보 리스트
            $this->set_page();

            $this->load->model('Enroll');
            $this->Enroll->user_id = $user_content['id'];
            $this->Enroll->get_not_end_only = true;
            $this->return_data['data']['enroll_list'] = $this->Enroll->get_index(5, 0, 'start_date');

            $this->load->model($this->model);
            $this->{$this->model}->user_id = $user_content['id'];
            $this->{$this->model}->facility_id = null;
            $this->{$this->model}->get_current_only = true;
            $list = $this->{$this->model}->get_index(5);
            $this->return_data['data']['rent_list'] = $list;

            $this->return_data['data']['user_content'] = $user_content;
            $this->setting_pagination(array('total_rows' => $list['total'], 'ep_id' => $id));
        }

        $this->layout->add_js('timepicki.js');
    }

    protected function get_start_end_time($data)
    {
        $data['start_datetime'] = $data['start_date'] . ' 00:00:01';
        $data['end_datetime'] = $data['end_date'] . ' 23:59:59';

        return $data;
    }

    protected function set_insert_data($data = null)
    {
        $data = parent::set_insert_data($data);

        if ($data['rent_month']) {
            $data['insert_quantity'] = $data['rent_month'];
        }

        if (empty($data['end_date'])) {
            $start_datetime_obj = new DateTime($data['start_date'], $this->timezone);
            $start_datetime_obj->modify('last day of this month');
            $data['end_date'] = $start_datetime_obj->format('Y-m-d');
        }

        return $this->get_start_end_time($data);
    }

    protected function after_insert_data($id, $data)
    {
        $account_insert = true;

        if ($account_insert) {
            $this->load->model('Account');
            $data['account_category_id'] = $this->add_id;
            $this->Account->insert($data);
        }

        if (!empty($data['order'])) {
            $insert_order = false;

            foreach ($data['order'] as $index => $order) {
                if (key_exists('product', $order)) {
                    $insert_order = true;
                } else {
                    unset($data['order'][$index]);
                }
            }

            if ($insert_order) {
                $order = $this->set_order_insert_data(array('list' => $data['order'], 'user_id' => $data['user_id'], 'transaction_date' => $data['transaction_date']));

                $this->load->model('Order');
                $this->load->model('OrderProduct');

                foreach ($order['order_product'] as $index => $value) {
                    $order_id = $this->Order->insert(array('user_id' => $data['user_id'], 'cash' => $value['cash'], 'credit' => $value['credit'], 'transaction_date' => $data['transaction_date'], 'price' => $value['total_price'], 'original_price' => $value['total_price'], 'payment' => $value['cash'] + $value['credit']));
                    $account_category_id = ADD_ORDER;

                    $value['order_id'] = $order_id;
                    $order_product_id = $this->OrderProduct->insert($value);

                    $this->Account->insert(array('account_category_id' => $account_category_id, 'user_id' => $data['user_id'], 'cash' => $value['cash'], 'credit' => $value['credit'], 'order_id' => $order_id, 'transaction_date' => $data['transaction_date'], 'product_id' => $value['product_id']));
                }
            }
        }

        parent::after_insert_data($id, $data);
    }

    protected function insert_complete_message($id)
    {
        return _('Successfully Rented Facility');
    }

    protected function add_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            $content = $this->get_view_content_data($id);

            return str_replace('_', '-', $this->router->fetch_class()) . '?facility_id=' . $content['facility_id'] . '&no=' . $content['no'] . '#facility-' . $content['no'];
        }
    }

    protected function edit_redirect_path($id)
    {
        return $this->add_redirect_path($id);
    }

    protected function set_form_validation($id = null)
    {
        parent::set_form_validation($id);

        if ($this->router->fetch_method() == 'add') {
            $this->form_validation->set_rules('user_id', _('User'), 'required|integer|callback_valid_user');
        }

        $this->form_validation->set_rules('facility_id', _('Facility'), 'required|integer|callback_valid_facility');
        $this->form_validation->set_rules('no', _('Facility No'), 'required|integer|callback_valid_facility_no[' . $id . ']');
        $this->form_validation->set_rules('rent_month', _('Rent Month'), 'integer');
        $this->form_validation->set_rules('free', _('Free'), 'integer|in_list[1]');
        $this->form_validation->set_rules('start_date', _('Start Date'), 'required|callback_valid_date');
        if ($this->input->post('end_date')) {
            $this->form_validation->set_rules('end_date', _('End Date'), 'callback_valid_date|callback_valid_date_after[' . $this->input->post('start_date') . ']');
        }
    }

    public function valid_user($user_id)
    {
        /// user check
        $this->load->model('User');
        $user_content = $this->User->get_content($user_id);

        if (empty($user_content)) {
            if (isset($this->form_validation)) {
                $this->form_validation->set_message('valid_user', sprintf(_('User ID(%d) Not Exists'), $user_id));
            }

            return false;
        }

        return true;
    }

    public function valid_facility($facility_id)
    {
        if(empty($facility_id)) {
            if (isset($this->form_validation)) {
                $this->form_validation->set_message('valid_facility', _('Select Facility First'));
            }
            return false;
        }

        // facility check
        $this->load->model('Facility');
        $facility_content = $this->Facility->get_content($facility_id);

        if (empty($facility_content)) {
            if (isset($this->form_validation)) {
                $this->form_validation->set_message('valid_facility', sprintf(_('Facility ID(%d) Not Exists'), $facility_id));
            }

            return false;
        }

        return true;
    }

    public function valid_facility_no($no, $id = null)
    {
        // 미정(0) 이면 상관없음
        if ($no == 0) {
            return true;
        }

        $data = $this->input->post(null, true);

        if (!empty($id)) {
            $this->load->model('Rent');
            $rent_content = $this->Rent->get_content($id);

            if (!empty($rent_content)) {
                if (empty($data['start_datetime'])) {
                    $data['start_datetime'] = $rent_content['start_datetime'];
                }

                if (empty($data['end_datetime'])) {
                    $data['end_datetime'] = $rent_content['end_datetime'];
                }
            }
        }

        $this->load->model('Facility');
        $facility_content = $this->Facility->get_content($data['facility_id']);

        if (empty($data['start_datetime']) and empty($data['end_datetime'])) {
            $data = $this->get_start_end_time($data);
        }

        if ($facility_content['start_no'] > $no) {
            $this->form_validation->set_message('valid_facility_no', sprintf(_('Facility(%s) No. must be greater than %d'), $facility_content['title'], $facility_content['start_no']));

            return false;
        }

        if (($facility_content['start_no'] + $facility_content['quantity']) < $no) {
            $this->form_validation->set_message('valid_facility_no', sprintf(_('Facility(%s) No. must be smaller than %d'), $facility_content['title'], $facility_content['start_no']));

            return false;
        }

        $rent_list = $this->get_available_list($facility_content['id'], $data['start_datetime'], $data['end_datetime']);
        $current_time_obj = new DateTIme('now', $this->timezone);

        if ($rent_list['total']) {
            $check_start_date_obj = new DateTime($data['start_datetime'], $this->timezone);
            $check_end_date_obj = new DateTime($data['end_datetime'], $this->timezone);

            foreach ($rent_list['list'] as $value) {
                // 자기것이면 넘어감
                if (!empty($id) and !empty($value['id'])) {
                    if ($value['id'] == $id) {
                        continue;
                    }
                }

                if ($value['no'] == $no and !empty($value['disable'])) {
                    if ($value['disable'] == _('Breakdown Facility')) {
                        $this->form_validation->set_message('valid_facility_no', sprintf(_('Facility(%s) No.%d is broken facility'), $value['product_name'], $value['no']));

                        return false;
                    }

                    $start_date_obj = new DateTime($value['start_datetime'], $this->timezone);
                    $end_date_obj = new DateTime($value['end_datetime'], $this->timezone);

                    if (($check_start_date_obj < $end_date_obj) and ($check_end_date_obj > $start_date_obj)) {
                        $this->form_validation->set_message('valid_facility_no', sprintf(_('Facility(%s) No.%d is reservation(%s~%s) By %s'), $facility_content['product_name'], $value['no'], $start_date_obj->format('Y-m-d'), $end_date_obj->format('Y-m-d'), $value['user_name']));
                    } else {
                        $this->form_validation->set_message('valid_facility_no', sprintf(_('Facility(%s) No.%d is in using By %s'), $facility_content['product_name'], $value['no'], $value['user_name']));
                    }

                    return false;
                }
            }
        }

        return true;
    }

    public function get_available_no($facility_id)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->form_validation->set_rules('current_order_id', _('Order'), 'integer');
        $this->form_validation->set_rules('move_current_rent_id', _('Rent'), 'integer');
        $this->form_validation->set_rules('start_date', _('Start Date'), 'callback_valid_date');
        $this->form_validation->set_rules('end_date', _('End Date'), 'callback_valid_date');
        $this->set_message();

        if ($this->form_validation->run() == false) {
            $this->render_format();
        } else {
            $start_datetime = null;
            $end_datetime = null;
            $current_order_id = null;

            if ($this->input->get('current_order_id')) {
                $current_order_id = $this->input->get('current_order_id');
            }

            if ($this->input->get('move_current_rent_id')) {
                $this->load->model('Rent');
                $rent_content = $this->Rent->get_content($this->input->get('move_current_rent_id'));

                if (!empty($rent_content)) {
                    $start_datetime = $rent_content['start_datetime'];
                    $end_datetime = $rent_content['end_datetime'];
                }
            }

            if ($this->input->get('start_date') and $this->input->get('end_date')) {
                $start_datetime = $this->input->get('start_date') . ' 00:00:01';
                $end_datetime = $this->input->get('end_date') . ' 23:59:59';
            }

            $list = $this->get_available_list($facility_id, $start_datetime, $end_datetime, $current_order_id);

            if ($this->format == 'json') {
                echo json_encode(array('result' => 'success', 'price' => $list['content']['price'], 'use_not_set' => $list['content']['use_not_set'], 'total' => $list['total'], 'list' => $list['list']));
            }
        }
    }

    protected function get_available_list($facility_id, $start_datetime = null, $end_datetime = null, $current_order_id = null)
    {
        $this->load->model('Facility');
        $content = $this->Facility->get_content($facility_id);

        $this->load->model($this->model);
        $this->{$this->model}->facility_id = $content['id'];

        if (!empty($start_datetime) and !empty($end_datetime)) {
            $this->{$this->model}->period_start_datetime = $start_datetime;
            $this->{$this->model}->period_end_datetime = $end_datetime;
        }

        if (!empty($current_order_id)) {
            $this->{$this->model}->not_order_id = $current_order_id;
        }
        $rent_list = $this->{$this->model}->get_index(10000, 0);

        $this->load->model('FacilityBreakdown');
        $this->FacilityBreakdown->facility_id = $content['id'];
        $facility_breakdown_list = $this->FacilityBreakdown->get_index($content['quantity'], 0);

        $list['content'] = $content;
        $list['total'] = 0;
        $list['list'] = array();
        $quantity = $content['quantity'];
        $start_no = $content['start_no'];
        $end_no = $quantity + $start_no;

        for ($no = $start_no; $no < $end_no; ++$no) {
            $disable = false;
            $rent_exists = false;
            $rent_start_date = null;
            $rent_end_date = null;
            $rent_start_datetime = null;
            $rent_end_datetime = null;
            $rent_user_name = null;

            if ($rent_list['total']) {
                foreach ($rent_list['list'] as $rent) {
                    if ($rent['no'] == $no) {
                        $rent_exists = true;
                        $rent_user_name = $rent['user_name'];
                        $rent_id = $rent['id'];

                        $rent_start_date = $rent['start_date'];
                        $rent_end_date = $rent['end_date'];
                        $rent_start_datetime = $rent['start_datetime'];
                        $rent_end_datetime = $rent['end_datetime'];
                        continue;
                    }
                }
            }

            if ($rent_exists) {
                $disable = _('Using Facility');
            }

            $facility_breakdown_exists = false;
            if ($facility_breakdown_list['total']) {
                foreach ($facility_breakdown_list['list'] as $breakdown) {
                    if ($breakdown['no'] == $no) {
                        $facility_breakdown_exists = true;
                        continue;
                    }
                }
            }

            if ($facility_breakdown_exists) {
                $disable = _('Breakdown Facility');
            }

            if ($disable) {
                if (isset($rent_id)) {
                    $list['list'][$no] = array('no' => $no, 'enable' => 0, 'disable' => $disable, 'user_name' => $rent_user_name, 'id' => $rent_id);
                } else {
                    $list['list'][$no] = array('no' => $no, 'enable' => 0, 'disable' => $disable, 'user_name' => $rent_user_name);
                }

                if (!empty($rent_start_date)) {
                    $list['list'][$no]['start_date'] = $rent_start_date;
                }

                if (!empty($rent_end_date)) {
                    $list['list'][$no]['end_date'] = $rent_end_date;
                }

                if (!empty($rent_start_datetime)) {
                    $list['list'][$no]['start_datetime'] = $rent_start_datetime;
                }

                if (!empty($rent_end_datetime)) {
                    $list['list'][$no]['end_datetime'] = $rent_end_datetime;
                }
            } else {
                $list['list'][$no] = array('no' => $no, 'enable' => 1);
            }
            ++$list['total'];
        }

        return $list;
    }

    public function move($id)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('facility_id', _('Facility'), 'required|integer');
        $this->form_validation->set_rules('no', _('Facility No'), 'required|integer|callback_valid_facility_no[' . $id . ']');

        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($id);
        $this->return_data['data']['content'] = $content;

        if ($this->form_validation->run() == false) {
            $this->load->model('Facility');
            $this->return_data['data']['facility'] = $this->Facility->get_index(100, 0);

            $this->return_data['data']['id'] = $content['id'];
            $this->return_data['data']['facility_available_no'] = $this->get_available_list($content['facility_id'], $content['start_datetime'], $content['end_datetime']);
            $this->script = 'rents/move.js';
            $this->render_format();
        } else {
            $data = $this->input->post(null,true);

            $this->load->model('Facility');
            $facility_content=$this->Facility->get_content($data['facility_id']);

            $this->load->model($this->model);
            $result = $this->{$this->model}->update(array('facility_id' => $facility_content['id'], 'no' => $data['no'], 'id' => $id));

            if ($result) {
                $this->load->model('OrderProduct');
                $this->OrderProduct->order_id=$content['order_id'];
                $this->OrderProduct->product_id=$content['product_id'];
                $product_orders=$this->OrderProduct->get_index();

                if(!empty($product_orders['total'])) {
                    if($product_orders['total']>3 ) {
                        throw new Exception('something wrong');
                    }
                    
                    foreach($product_orders['list'] as $product_order) {
                        $this->OrderProduct->update(array('product_id'=>$facility_content['product_id'],'id'=>$product_order['id']));
                    }
                }


                $this->session->set_flashdata('message', array('type' => 'success', 'message' => _('Successfully Moved Rent')));

                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect('/rents/move/' . $content['id']);
            }
        }
    }

    protected function set_end_insert_data($data)
    {
        if (!empty($data['end_date'])) {
            if (!empty($data['end_now'])) {
                $data['end_datetime'] = $this->now;
                $data['order_end'] = 1;
            } else {
                $data['end_datetime'] = $data['end_date'] . ' 23:59:59';
            }
        }

        return $data;
    }

    protected function get_view_data($id)
    {
        $this->script = 'rents/sync-content.js';

        return $this->get_view_content_data($id);
    }

    protected function render_form_resource()
    {
        $this->layout->add_css('timepicki.css');
        $this->layout->add_js('timepicki.js');
        if ($this->edit_script) {
            $this->layout->add_js($this->edit_script . '?version=' . $this->assets_version);
        }
    }

    public function export_excel($user_id)
    {
        $this->load->model($this->model);
        $this->{$this->model}->user_id = $user_id;

        if ($this->input->get('end_rent')) {
            $this->{$this->model}->get_end_only = true;
        }

        $list = $this->{$this->model}->get_index(100, 0);

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
            ->setCellValue('C1', _('Facility'))
            ->setCellValue('D1', _('Facility No'))
            ->setCellValue('E1', _('Period'))
            ->setCellValue('F1', _('Start Time'))
            ->setCellValue('G1', _('End Time'))
            ->setCellValue('H1', _('Price'))
            ->setCellValue('I1', _('Payment'))
            ->setCellValue('J1', _('Memo'));

        if ($list['total']) {
            foreach ($list['list'] as $index => $value) {
                $start_date_obj = new DateTime($value['start_datetime'], $this->timezone);
                $end_date_obj = new DateTime($value['end_datetime'], $this->timezone);
                $current_date_obj = new DateTime('now', $this->timezone);

                $status = _('Using');

                if ($current_date_obj > $start_date_obj) {
                    if ($end_date_obj < $current_date_obj) {
                        $status = _('Expired');
                    }
                } else {
                    $status = _('Reservation');
                }

                if ($value['stopped']) {
                    $status = _('Stopped');
                }

                $memo = '-';
                if (!empty($value['content'])) {
                    $memo = $value['content'];
                }

                $remain_date = '-';
                $remain_count = '-';

                if (empty($value['insert_quantity'])) {
                    $rent_period = get_period($value['start_datetime'], $value['end_datetime'], $this->timezone);
                } else {
                    $rent_period =  $value['insert_quantity'] . ' ' . _('Period Month');
                }

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($index + 2), $value['transaction_date'])
                    ->setCellValue('B' . ($index + 2), $status)
                    ->setCellValue('C' . ($index + 2), $value['product_name'])
                    ->setCellValue('D' . ($index + 2), $value['no'])
                    ->setCellValue('E' . ($index + 2), $rent_period)
                    ->setCellValue('F' . ($index + 2), $value['start_date'])
                    ->setCellValue('G' . ($index + 2), $value['end_date'])
                    ->setCellValue('H' . ($index + 2), $value['price'])
                    ->setCellValue('I' . ($index + 2), $value['payment'])
                    ->setCellValue('J' . ($index + 2), $memo);
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

    protected function delete_complete_message(array $content)
    {
        return _('Successfully Delete Rent Facility');
    }
}
