<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Pagination_aside.php';

class Home extends SL_Controller
{
    use Pagination_aside;

    protected $model = 'User';
    protected $use_index_content = true;
    protected $script = 'home/index.js';
    protected $permission_controller = 'users';

    public function show_omu()
    {
        if (empty($this->input->post('show_omu'))) {
            $this->session->unset_userdata('show_omu');
        } else {
            $this->session->set_userdata('show_omu', true);
        }

        echo json_encode(array('result' => 'success'));
    }

    public function enrolls($id)
    {
        if ($content = $this->common_index($id)) {
            $enroll = $this->get_enroll_list();

            $this->load->model('OrderTransfer');
            $this->OrderTransfer->user_id = $id;
            $this->OrderTransfer->enroll = true;
            $enroll_transfer_list = $this->OrderTransfer->get_index(10, 0);

            $this->return_data['data']['enroll'] = $this->add_enroll_in($enroll, $id);

            if (isset($enroll['content'])) {
                $this->load->model('Account');
                $this->Account->order_id = $enroll['content']['order_id'];
                $this->Account->no_commission = true;
                $this->Account->no_branch_transfer = true;
                $this->return_data['data']['log'] = $this->Account->get_index(10, 0);
            } else {
                $this->return_data['data']['log']['total'] = 0;
            }

            $enroll_end_list['total'] = 0;

            $this->return_data['data']['end_term'] = 3;
            $this->return_data['data']['end_list'] = $enroll_end_list;
            $this->return_data['data']['transfer_list'] = $enroll_transfer_list;
        }

        $this->render_format();
    }

    public function rents($id)
    {
        if ($content = $this->common_index($id)) {
            $rent = $this->get_rent_list();

            $this->load->model('OrderTransfer');
            $this->OrderTransfer->user_id = $id;
            $this->OrderTransfer->rent = true;
            $this->return_data['data']['transfer_list'] = $this->OrderTransfer->get_index(10, 0);

            $this->Rent->no_display_only = true;

            $this->return_data['data']['rent'] = $rent;

            if (isset($rent['content'])) {
                $this->load->model('Account');
                $this->Account->order_id = $rent['content']['order_id'];
                $this->return_data['data']['log'] = $this->Account->get_index(10, 0);
            } else {
                $this->return_data['data']['log']['total'] = 0;
            }

            $this->Rent->display_only = null;
            $this->Rent->no_display_only = true;
            $this->return_data['data']['end_list'] =  $this->Rent->get_index(100, 0);
        }

        $this->render_format();
    }

    public function rent_sws($id)
    {
        if ($content = $this->common_index($id)) {
            $rent_sws = $this->get_rent_sws_list();

            $this->return_data['data']['rent_sws'] = $rent_sws;
            $this->RentSw->display_only = null;
            $this->RentSw->no_display_only = true;
            $this->return_data['data']['end_list']=$this->RentSw->get_index(10, 0);
        }

        $this->render_format();
    }

    public function accounts($id)
    {
        if ($content = $this->common_index($id)) {
            $this->load->model('Account');
            $this->Account->user_id = $this->return_data['data']['content']['id'];
            $this->Account->no_commission = true;
            $this->Account->no_branch_transfer = true;
            $this->return_data['data']['account'] = $this->Account->get_index(10, 0);
            $this->script = 'home/accounts.js';
        }

        $this->render_format();
    }

    public function memo($id)
    {
        if ($content = $this->common_index($id)) {
            $this->load->model('UserContent');
            $this->UserContent->user_id = $content['id'];
            $this->return_data['data']['memo'] = $this->UserContent->get_index(100, 0);

            $this->load->model('OrderContent');
            $this->OrderContent->user_id = $content['id'];
            $this->return_data['data']['order_memo'] = $this->OrderContent->get_index(100, 0);

            $this->load->model('OrderEditLog');
            $this->OrderEditLog->user_id = $content['id'];
            $this->return_data['data']['order_edit_log'] = $this->OrderEditLog->get_index(100, 0);
        }

        $this->render_format();
    }

    public function attendances($id)
    {
        if ($content = $this->common_index($id)) {
            $this->load->model('Entrance');
            $this->Entrance->user_id = $id;
            $this->return_data['data']['list'] = $this->Entrance->get_index(10, 0);
            $this->return_data['data']['attendance'] = $this->Entrance->get_attendance_by_user($this->return_data['data']['content']['id']);

            $this->return_data['data']['today_list'] = $this->Entrance->get_attendance_by_user_n_date($this->return_data['data']['content']['id'], $this->date);
            $this->script = 'home/attendance.js';
        }

        $this->render_format();
    }

    public function body_indexes($id)
    {
        if ($content = $this->common_index($id)) {
            $this->load->model('UserWeight');
            $this->UserWeight->user_id = $content['id'];
            $this->return_data['data']['list'] = $this->UserWeight->get_index(10, 0);

            $this->load->model('UserHeight');
            $this->UserHeight->user_id = $content['id'];
            $this->return_data['data']['height_list'] = $this->UserHeight->get_index(10, 0);
        }

        $this->render_format();
    }

    protected function get_enroll_list($start_only = false)
    {
        $this->load->model('Enroll');
        $this->Enroll->user_id = $this->return_data['data']['content']['id'];

        if (empty($start_only)) {
            $this->Enroll->get_current_only = false;
        } else {
            $this->Enroll->get_start_only = true;
        }

        $this->Enroll->edit_log_count = true;
        $enroll = $this->Enroll->get_index(1000, 0, 'have_datetime,id');

        if ($this->input->get('enroll_id')) {
            $enroll['content'] = $this->Enroll->get_content($this->input->get('enroll_id'));
        } else {
            if (!empty($enroll['total'])) {
                $enroll['content'] = $enroll['list'][0];
            }
        }

        return $enroll;
    }

    protected function get_rent_list()
    {
        $this->load->model('Rent');
        $this->Rent->display_only = true;

        $this->Rent->user_id = $this->return_data['data']['content']['id'];

        $rent = $this->Rent->get_index(100, 0, 'r.start_datetime');

        if ($this->input->get('rent_id')) {
            $rent['content'] = $this->Rent->get_content($this->input->get('rent_id'));
        } else {
            if ($rent['total']) {
                $rent['content'] = $rent['list'][0];
            }
        }

        return $rent;
    }

    protected function get_rent_sws_list()
    {
        $this->load->model('RentSw');
        $this->RentSw->display_only = true;

        $this->RentSw->user_id = $this->return_data['data']['content']['id'];

        $rent_sws = $this->RentSw->get_index(100, 0, 'rsw.start_date');

        if ($this->input->get('rent_sws_id')) {
            $rent_sws['content'] = $this->RentSw->get_content($this->input->get('rent_sws_id'));
        } else {
            if ($rent_sws['total']) {
                $rent_sws['content'] = $rent_sws['list'][0];
            }
        }

        return $rent_sws;
    }

    protected function add_enroll_in($enroll_list, $user_id)
    {
        if (empty($enroll_list['total'])) {
            return $enroll_list;
        }

        $p_enroll_list = $this->get_primary_all($user_id);

        if (empty($p_enroll_list['total'])) {
            return $enroll_list;
        }

        foreach ($enroll_list['list'] as $index => $enroll) {
            foreach ($p_enroll_list['list'] as $index2 => $p_enroll) {
                if ($enroll['order_id'] == $p_enroll['order_id']) {
                    $enroll_list['list'][$index]['in'] = $p_enroll_list['total'] - $index2;
                }
            }
        }

        return $enroll_list;
    }

    protected function get_primary_all($user_id)
    {
        $this->load->model('Enroll');
        $this->Enroll->user_id = $user_id;
        $this->Enroll->primary_only = true;
        $this->Enroll->get_current_only = false;
        $this->Enroll->get_not_end_only = false;

        return $this->Enroll->get_index(100, 0, 'e.have_datetime,e.id');
    }

    protected function humake_remake_stop_list($stopping_list, $user_id)
    {
        if (empty($stopping_list['total'])) {
            return $stopping_list;
        }

        $enrolls = $this->get_primary_all($user_id);

        if ($enrolls['total']) {
            $current_user_stop_id = $stopping_list['list'][0]['user_stop_id'];
            $user_stop_order = 1;

            foreach ($stopping_list['list'] as $index => $stopped_order) {
                if ($stopped_order['user_stop_id'] != $current_user_stop_id) {
                    ++$user_stop_order;
                    $current_user_stop_id = $stopped_order['user_stop_id'];
                }

                $stopping_list['list'][$index]['stop_order'] = $user_stop_order;
            }
            $stopping_list['total_stop_order'] = $user_stop_order;
        } else {
            $stopping_list['total'] = 0;
            unset($stopping_list['list']);
        }

        return $stopping_list;
    }

    public function stops($id)
    {
        $this->common_index($id);

        $order_list = $this->add_enroll_in($this->get_current_primary_order($id), $id);

        if(!empty($order_list['total'])) {
            $this->load->model('UserStopCustom');
            foreach($order_list['list'] as $index=>$value) {
                $userStopCustom=$this->UserStopCustom->get_content_by_parent_id($value['order_id']);

                if(!empty($userStopCustom)) {
                    $order_list['list'][$index]['total_stop_day_count']=$order_list['list'][$index]['total_stop_day_count']+$userStopCustom['custom_days'];
                }
            }
        }

        if($this->session->userdata('role_id')<3) {
            $admin_order_list=$this->add_enroll_in($this->get_current_primary_order($id,true), $id);
            $this->return_data['data']['admin_order_list']=$admin_order_list;
        }

        $this->return_data['data']['order_list']=$order_list;

        $this->load->model('UserStop');
        $this->UserStop->parent_id = $id;
        $this->UserStop->current_only = true;
        $user_stopping_list = $this->add_enroll_in($this->UserStop->get_index(100, 0, 0, 'stop_start_date', true), $id);

        if ($user_stopping_list['total']) {
            $this->load->model('OrderStop');
            $this->OrderStop->user_id = $id;
            $this->OrderStop->current_only = true;
            $this->OrderStop->enroll = true;
            $stopping_orders = $this->add_enroll_in($this->OrderStop->get_index(100, 0, 'stop_start_date', true), $id);
        } else {
            $stopping_orders = array('total' => 0);
        }

        $this->load->model('UserStopSchedule');
        $this->UserStopSchedule->user_id = $id;
        $stop_schedules = $this->UserStopSchedule->get_index(100, 0);

        $this->load->model('OrderStopLog');
        $this->OrderStopLog->user_id = $id;
        $this->OrderStopLog->get_user_stop_id = true;
        $stopped_logs = $this->add_enroll_in($this->OrderStopLog->get_index(100, 0, 'stop_start_date', true), $id);

        $this->return_data['data']['stopping_list'] = $this->humake_remake_stop_list($stopping_orders, $id);
        $stopped_logs = $this->humake_remake_stop_list($stopped_logs, $id);
        $this->return_data['data']['stop_schedules'] = $stop_schedules;

        if ($stopped_logs['total']) {
            $this->UserStop->parent_id = $id;
            $this->UserStop->current_only = false;
            $this->UserStop->ended_only = true;
            $this->return_data['data']['user_stop_list'] = $this->add_enroll_in($this->UserStop->get_index(100, 0, 0, 'stop_start_date', true), $id);
        }

        $this->return_data['data']['stopped_log'] = $this->add_enroll_in($stopped_logs, $id);

        if ($stopping_orders['total']) {
            $available_add = true;
            foreach ($stopping_orders['list'] as $stopping_order) {
                if (empty($stopping_order['stop_end_date'])) {
                    $available_add = false;
                }
            }

            $this->return_data['data']['available_add'] = $available_add;

            $user_stop_content = false;

            if ($user_stopping_list['total']) {
                $user_stop_content = $user_stopping_list['list'][0];
            }

            $this->return_data['data']['user_stop_content'] = $user_stop_content;
        }

        $this->return_data['data']['user_stopping_list'] = $user_stopping_list;

        $this->render_format();
    }

    public function reservations($id)
    {
        if ($this->format == 'json') {
            $this->set_page();

            $this->load->model('ReservationUser');
            $this->ReservationUser->reservation_info = true;
            $this->ReservationUser->user_id = $id;
            $list=$this->ReservationUser->get_index($this->per_page, $this->page);
        } else {
            if ($content = $this->common_index($id)) {
                $this->load->model('ReservationUser');
                $this->ReservationUser->reservation_info = true;
                $this->ReservationUser->user_id = $content['id'];
                $list = $this->ReservationUser->get_index(10, 0);
                $this->script = 'home/reservations.js';
            }
            $this->script = 'home/reservations.js';
        }
        
        if (!empty($list['total'])) {
            $current_month_first_day = new DateTime($this->today, $this->timezone);
            $current_month_first_day->modify('first day of this month');
    
            $prev_month_first_day = new DateTime($this->today, $this->timezone);
            $prev_month_first_day->modify('first day of previous month');
            
            $current_month_10_day = new DateTime($this->today, $this->timezone);
            $current_month_10_day->modify('first day of this month');
            $current_month_10_day->modify('+9 Days');  

            foreach ($list['list'] as $index=>$value) {
                $delete_available=false;

                if ($this->session->userdata('role_id') < 3) {
                    $delete_available = true;
                } else {
                    if ($this->session->userdata('role_id') > 5) {
                        if($value['manager_id']==$this->session->userdata('admin_id')) {
                        if (new DateTime($value['end_time'], $this->timezone) >= new DateTime($this->today,$this->timezone)) {
                              $delete_available=true;
                          }
                        }
                      } else {
                        if (new DateTime($value['end_time'], $this->timezone) >= $current_month_first_day) {
                            $delete_available = true;
                        } else {
                            if (new DateTime($value['end_time'], $this->timezone) >= $prev_month_first_day) {
                                if (new DateTime($this->today, $this->timezone) <= $current_month_10_day) {
                                    $delete_available = true;
                                }
                            }
                        }
                    }
                }

                $list['list'][$index]['delete_available']=$delete_available;
            }
        }

        if ($this->format == 'json') {
            $this->return_data['data'] = $list;
        } else {
            $this->return_data['data']['list'] = $list;
        }

        $this->render_format();
    }

    public function messages($id)
    {
        if ($this->format == 'json') {
            $this->set_page();

            $this->load->model('Message');
            $this->Message->user_id = $id;
            $this->return_data['data'] = $this->Message->get_index($this->per_page, $this->page);
        } else {
            if ($content = $this->common_index($id)) {
                $this->load->model('Message');
                $this->Message->user_id = $content['id'];
                $this->Message->all_too = true;
                $this->return_data['data']['list'] = $this->Message->get_index(10, 0);
                $this->script = 'home/messages.js';
            }
        }

        $this->render_format();
    }

    public function view($id = null)
    {
        if (empty($id)) {
            show_404();
        }

        $this->common_index($id);

        if (isset($this->return_data['data']['content']['id'])) {
            $this->get_other_content($this->return_data['data']['content']['id']);
        }

        $this->render_format(array('result' => 'success', 'content' => $this->return_data['data']['content']));
    }

    public function ac_sync($id)
    {
        if (empty($id)) {
            show_404();
        }

        $this->render_format();
    }

    protected function index_data($category_id = null)
    {
        $this->common_index();

        if (isset($this->return_data['data']['content']['id'])) {
            $this->get_other_content($this->return_data['data']['content']['id']);
        }

        $this->default_view_file = 'view';
    }

    private function get_other_content($id)
    {
        $this->load->model('Rent');
        $this->Rent->display_only = true;

        $this->Rent->user_id = $id;
        $rents = $this->Rent->get_index(1, 0, 'start_date');

        if ($rents['total']) {
            $rent_content = $rents['list'][0];
        } else {
            $rent_content = false;
        }

        $this->return_data['other_data']['rent'] = $rent_content;

        $this->load->model('Entrance');
        $this->Entrance->user_id = $id;
        $entrances = $this->Entrance->get_index(1, 0);

        if ($entrances['total']) {
            $entrance_content = $entrances['list'][0];
        } else {
            $entrance_content = false;
        }

        $this->return_data['other_data']['entrance'] = $entrance_content;

        $this->load->model('Enroll');
        $this->Enroll->user_id = $id;
        $this->Enroll->get_not_end_only = true;

        $this->Enroll->lesson_type = 4;
        $this->return_data['other_data']['pt'] = $this->Enroll->get_index(5, 0,'have_datetime,id');

        $this->Enroll->lesson_type = array(1, 2, 3, 5);
        $this->return_data['other_data']['enroll'] = $this->Enroll->get_index(5, 0,'have_datetime,id');

        $this->load->model('Account');
        $this->Account->user_id = $id;
        $this->Account->no_commission = true;
        $this->Account->no_branch_transfer = true;
        $this->return_data['data']['account'] = $this->Account->get_index(5, 0);
        
        $this->load->model('RentSw');
        $this->RentSw->user_id = $id;
        $this->RentSw->get_current_only = true;
        $rent_sws=$this->RentSw->get_index(1, 0);
        if ($rent_sws['total']) {
            $rent_sw_content = $rent_sws['list'][0];
        } else {
            $rent_sw_content = false;
        }

        $this->return_data['other_data']['rent_sw'] = $rent_sw_content;

        $this->load->model('UserContent');
        $this->UserContent->user_id = $id;
        $this->return_data['other_data']['memo'] = $this->UserContent->get_index(3, 0);
    }

    private function common_index($id = null)
    {
        $this->set_page();

        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());

        $this->form_validation->set_rules('search_field', _('Search Field'), 'in_list[name,card_no,phone]');
        $this->form_validation->set_rules('search_word', _('Search Word'), 'trim');

        if ($this->form_validation->run() == false) {
            $list = $this->get_user_list($this->per_page, $this->page, false);
        } else {
            $list = $this->get_user_list($this->per_page, $this->page);
        }

        if ($this->input->get('search_type')) {
            $this->return_data['search_data']['search_type'] = $this->input->get('search_type');

            if ($this->input->get('search_field')) {
                $this->session->set_userdata('search_field', $this->input->get('search_field'));
            }
        }

        $content = false;

        if (empty($id)) {
            $content = $this->get_list_view_data($list);
        } else {
            $content = $this->User->get_content($id);
        }

        if ($this->router->fetch_method() == 'index') {
            $this->setting_pagination(array('base_url' => base_url() . 'home', 'total_rows' => $this->return_data['data']['user']['total']));
        } else {
            if ($this->router->fetch_method() == 'view') {
                $this->setting_pagination(array('base_url' => base_url() . 'view/' . $id, 'total_rows' => $this->return_data['data']['user']['total']));
            } else {
                $this->setting_pagination(array('base_url' => base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $id, 'total_rows' => $this->return_data['data']['user']['total']));
            }
        }

        if (!empty($content)) {
            $this->load->model('UserAdditional');
            $additional_content = $this->UserAdditional->get_content_by_parent_id($content['id']);

            $this->load->model('UserTransfer');
            $user_transfer_content = $this->UserTransfer->get_content_by_parent_id($content['id']);

        if($this->input->get('show_count')) {
            $this->return_data['data']['show_count']=true;

            $temp_cookie_name='temp_'.$content['id'];

            $value = $this->input->cookie($temp_cookie_name, true); // 두 번째 인자 true → XSS 필터 적용
            if ($value === null) {
                $this->load->model('Entrance');
                $entrance_data=$this->set_entrance_insert_data(array('user_id'=>$content['id'],'date'=>$this->today));
                $this->Entrance->insert($entrance_data);

                $cookie = array(
                    'name'   => $temp_cookie_name,
                    'value'  => true,
                    'expire' => 60,      // 초 단위 (1분)
                    'secure' => false,   // HTTPS에서만 전송하려면 true
                    'httponly' => true,  // JS 접근 방지
                );

                $this->input->set_cookie($cookie);
            }

            $this->load->model('Enroll');
            $this->Enroll->user_id=$content['id'];
            $this->Enroll->primary_only=true;
            $this->Enroll->get_current_only=true;
            $this->return_data['data']['current_enroll']=$this->Enroll->get_index();
        }

        }

        if (!empty($additional_content)) {
            $this->return_data['data']['additional_content'] = $additional_content;
            $content = array_merge($additional_content, $content);
        }

        if (!empty($user_transfer_content['enable'])) {
            $this->return_data['data']['user_transfer_content'] = $user_transfer_content;
        }

        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
        $this->return_data['data']['content'] = $content;
        $this->return_data['data']['user_content'] = $content;

        return $content;
    }

    private function set_entrance_insert_data($data)
    {
        $date = $data['date'];

        if (empty($data['time'])) {
            $time = date('H:i:s');
        } else {
            $time = $data['time'];
        }

        $data['in_time'] = $date . ' ' . $time;

        return $data;
    }

    private function get_current_primary_order($id, $admin=false)
    {
        $this->load->model('Enroll');
        $this->Enroll->user_id = $id;
        
        if (empty($admin)) {
            $this->Enroll->get_not_end_only = true;
        } else {
            $this->Enroll->get_current_only = false;
            $this->Enroll->get_not_end_only = false;
        }
        $this->Enroll->primary_only = true;

        return $this->Enroll->get_index(1000, 0,'e.have_datetime,e.id');
    }
}
