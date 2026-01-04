<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order_stops.php';
require_once 'Ac_sync.php';
require_once 'Push.php';

class User_stops extends Order_stops
{
    use Ac_sync;
    use Push;

    protected $parent_model = 'User';
    protected $model = 'UserStop';
    protected $permission_controller = 'users';
    protected $script = 'user-stops/add.js';
    protected $schedule = false;

    protected function set_add_form_data()
    {
        $parent_id = $this->uri->segment(3);

        $this->load->model($this->parent_model);
        $content = $this->{$this->parent_model}->get_content($parent_id);

        $this->load->model('UserStop');
        $this->UserStop->user_id = $content['id'];
        $user_stops = $this->UserStop->get_index();

        $this->load->model('UserStopSchedule');
        $this->UserStopSchedule->user_id = $content['id'];
        $user_stop_schedules = $this->UserStopSchedule->get_index();

        if($this->input->get_post('order_id')) {
            $this->load->model('Enroll');
            $this->return_data['data']['return_url']='/home/stops/'.$content['id'];
            $this->return_data['data']['enroll_content']=$this->Enroll->get_content_by_order_id($this->input->get_post('order_id'));
        }
        
        if($this->input->get_post('request_id')) {
            $this->load->model('UserStopRequest');
            $this->return_data['data']['request_content']=$this->UserStopRequest->get_content($this->input->get_post('request_id'));

            $order_list = $this->add_enroll_in($this->get_current_primary_order($parent_id), $parent_id);

            if(!empty($order_list['total'])) {
                $this->load->model('UserStopCustom');
                foreach($order_list['list'] as $index=>$value) {
                    $userStopCustom=$this->UserStopCustom->get_content_by_parent_id($value['order_id']);
    
                    if(!empty($userStopCustom)) {
                        $order_list['list'][$index]['total_stop_day_count']=$order_list['list'][$index]['total_stop_day_count']+$userStopCustom['custom_days'];
                    }
                }
            }
    
            $this->return_data['data']['order_list']=$order_list;
        }

        $this->return_data['data']['user_stops'] = $user_stops;
        $this->return_data['data']['user_stop_schedules'] = $user_stop_schedules;
        $this->return_data['data']['content'] = $content;
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

    private function get_current_primary_order($id, $admin=false)
    {
        $this->load->model('Enroll');
        $this->Enroll->user_id = $id;
        
        if(!empty($this->session->userdata('center_id'))) {
            $this->Enroll->center_id = $this->session->userdata('center_id');
        }
        
        if (empty($admin)) {
            $this->Enroll->get_not_end_only = true;
        } else {
            $this->Enroll->get_current_only = false;
            $this->Enroll->get_not_end_only = false;
        }
        $this->Enroll->primary_only = true;

        $indexes=$this->Enroll->get_index(1000, 0,'e.have_datetime,e.id');

        return $indexes;
    }

    protected function set_edit_form_data(array $content)
    {
        $this->default_view_file = 'edit.php';
        $this->script = 'user-stops/edit.js';
        $this->return_data['data']['content'] = $content;
    }

    protected function is_schedule($stop_start_date)
    {
        $start_date_obj = new DateTime($stop_start_date, $this->timezone);

        $date_today = new DateTime($this->today, $this->timezone);
        $interval = $start_date_obj->diff($date_today);

        if ($interval->format('%R') == '-') { // 중지시작일이 지금이 아니면(중지 예약)
            $this->schedule = true;

            return true;
        }

        return false;
    }

    protected function get_stop_day_count($stop_start_date, $stop_end_date)
    {
        $start_date_obj = new DateTime($stop_start_date, $this->timezone);
        $end_date_obj = new DateTime($stop_end_date, $this->timezone);
        $interval_day_count = $start_date_obj->diff($end_date_obj);

        return intval($interval_day_count->format('%a')) + 1;
    }

    protected function set_insert_data($data)
    {
        if (empty($data['parent_id'])) {
            $parent_id = $this->uri->segment(3);
        } else {
            $parent_id = $data['parent_id'];
        }

        if (empty($data['stop_end_date'])) {
            $data['stop_end_date'] = null;
            $data['stop_day_count'] = 0;
        } else {
            $data['stop_day_count'] = $this->get_stop_day_count($data['stop_start_date'], $data['stop_end_date']);
        }

        if (!empty($data['is_today'])) {
            $data['request_date'] = $this->today;
        }

        if ($this->is_schedule($data['stop_start_date'])) {
            $data['schedule_date'] = $data['stop_start_date'];
            $data['enable'] = 0;
        }

        $data['user_id'] = $parent_id;

        return $data;
    }

    protected function set_update_data($id, $data)
    {
        if (!empty($data['is_today'])) {
            $data['request_date'] = $this->today;
        }

        if (empty($data['stop_end_date'])) {
            $data['stop_end_date'] = null;
            $data['stop_day_count'] = 0;
        } else {
            $data['stop_day_count'] = $this->get_stop_day_count($data['stop_start_date'], $data['stop_end_date']);
        }

        $data['id'] = $id;

        return $data;
    }

    protected function get_enrolls($user_id,$stop_start_date) {
        $this->load->model('Enroll');
        $this->Enroll->user_id = $user_id;
        $this->Enroll->period_start_date = $stop_start_date;
        return $this->Enroll->get_index(1000, 0);
    }

    protected function after_insert_data($id, $data)
    {
        // 중복되어서 부작용 발생하여 제거해야함
        unset($data['order_id']);

        if(!empty($data['request_id'])) {
            $this->load->model('UserStopRequest');
            $this->UserStopRequest->update(array('complete'=>1,'id'=>$data['request_id']));

            $send_users = array();
            $send_users[] = $data['user_id'];
    
            $message=array('title' => _('신청하신 휴회가 처리되었습니다'), 'content' => _('신청하신 휴회가 처리되었습니다'));
            $this->send_message_and_insert_log($send_users,$message);
        }

        if (!empty($data['schedule_date'])) {  // 스케쥴에 등록
            $this->load->model('UserStopSchedule');
            $this->UserStopSchedule->insert(array('user_stop_id' => $id, 'schedule_date' => $data['schedule_date']));

            return true;
        }

        $this->load->model('OrderStop');

        $enrolls = $this->get_enrolls($data['user_id'],$data['stop_start_date']);

        $fail_count = 0;
        if (!empty($enrolls['total'])) {
            $this->parent_model = 'Enroll';
            foreach ($enrolls['list'] as $enroll) {
                if (in_array($enroll['lesson_type'], array(4, 5))) {
                    continue;
                }

                $data['parent_id'] = $enroll['id'];
                $enroll_insert_data = parent::set_insert_data($data);
                $enroll_insert_data['parent_id'] = $enroll_insert_data['order_id'];
                $enroll_insert_data['user_stop_id'] = $id;

                if ($order_stop_id = $this->OrderStop->insert($enroll_insert_data)) {
                    parent::after_insert_data($order_stop_id, $enroll_insert_data);
                } else {
                    ++$fail_count;
                }
            }
        }

        $this->load->model('Rent');
        $this->Rent->user_id = $data['user_id'];
        $this->Rent->period_start_datetime = $data['stop_start_date'];
        $rents = $this->Rent->get_index(1000, 0);

        if (!empty($rents['total'])) {
            $this->parent_model = 'Rent';
            foreach ($rents['list'] as $rent) {
                $data['parent_id'] = $rent['id'];
                $rent_insert_data = parent::set_insert_data($data);
                $rent_insert_data['parent_id'] = $rent_insert_data['order_id'];
                $rent_insert_data['user_stop_id'] = $id;

                if ($order_stop_id = $this->OrderStop->insert($rent_insert_data)) {
                    parent::after_insert_data($order_stop_id, $rent_insert_data);
                } else {
                    ++$fail_count;
                }
            }
        }

        $this->load->model('RentSw');
        $this->RentSw->user_id = $data['user_id'];
        $this->RentSw->period_start_datetime = $data['stop_start_date'];
        $rent_sws = $this->RentSw->get_index(1000, 0);

        if (!empty($rent_sws['total'])) {
            $this->parent_model = 'RentSw';
            foreach ($rent_sws['list'] as $rent_sw) {
                $data['parent_id'] = $rent_sw['id'];
                $rent_sw_insert_data = parent::set_insert_data($data);
                $rent_sw_insert_data['parent_id'] = $rent_sw_insert_data['order_id'];
                $rent_sw_insert_data['user_stop_id'] = $id;

                if ($order_stop_id = $this->OrderStop->insert($rent_sw_insert_data)) {
                    parent::after_insert_data($order_stop_id, $rent_sw_insert_data);
                } else {
                    ++$fail_count;
                }
            }
        }

        if (!empty($data['stop_end_date'])) {
            $end_date_obj = new DateTime($data['stop_end_date'], $this->timezone);

            $date_today = new DateTime($this->today, $this->timezone);
            $interval = $date_today->diff($end_date_obj);

            // 끝난것이면
            if ($interval->format('%R') == '-') {
                $this->load->model($this->model);
                $content = $this->{$this->model}->get_content($id);

                $this->resume_p_process($data, $content);
            }
        }

        $result = false;
        if (empty($fail_count)) {
            $result = true;
        }

        if (!empty($enrolls['total'])) {
            $this->sync_access_controller($enroll_insert_data['user_id']);
        }

        return $result;
    }

    protected function after_update_data($id, $data)
    {
        $this->load->model('OrderStop');
        $this->OrderStop->user_id = $data['edit_content']['user_id'];
        $this->OrderStop->current_only = true;
        $order_stops = $this->OrderStop->get_index();

        if ($order_stops['total']) {
            $this->load->model('Order');
            foreach ($order_stops['list'] as $index => $value) {
                $this->OrderStop->delete($value['id']);
                $this->Order->update(array('id' => $value['order_id'], 'stopped' => 0));
            }
        }

        $data['user_id'] = $data['edit_content']['user_id'];
        $this->after_insert_data($id, $data);
    }

    public function resume($id)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('stop_start_date', _('Stop Start Date'), 'required|callback_valid_date');
        $this->form_validation->set_rules('stop_end_date', _('Stop End Date'), 'callback_valid_date|callback_valid_date_after[' . $this->input->post('stop_start_date') . ']');
        $this->set_message();

        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($id);

        if ($this->form_validation->run() == false) {
            $this->{$this->model}->user_id = $content['user_id'];
            $user_stops = $this->{$this->model}->get_index();

            $this->return_data['data']['user_stops'] = $user_stops;
            $this->return_data['data']['content'] = $content;

            $this->script = 'user-stops/resume.js';
            $this->render_format();
        } else {
            $data = $this->input->post(null, true);

            $this->resume_p_process($data, $content);
            $this->resume_after_process($data, $content);

            $this->sync_access_controller($content['user_id']);

            $result = false;
            if (empty($fail_count)) {
                $result = true;
            }

            if ($result) {
                $this->session->set_flashdata('message', array('type' => 'success', 'message' => sprintf(_('Successfully All Resume %s`s Order'), $content['name'])));
                redirect('/home/stops/' . $content['user_id']);
            } else {
                redirect('/home/stops/' . $content['user_id']);
            }
        }
    }

    protected function resume_after_process(array $data, array $content)
    {
        if (($data['stop_end_date'] == $content['stop_start_date']) and ($data['stop_end_date'] == $this->today)) {
            $this->{$this->model}->delete($content['id']);
        } else {
            $this->{$this->model}->update(array('id' => $content['id'], 'updated_at' => $this->now));
        }
    }

    protected function resume_p_process(array $data, array $content)
    {
        $this->load->model('UserStop');
        $this->UserStop->update(array('id' => $content['id'], 'stop_end_date' => $data['stop_end_date'], 'stop_day_count' => $this->get_stop_day_count($data['stop_start_date'], $data['stop_end_date']), 'updated_at' => $this->now));

        $fail_count = 0;

        $this->load->model('Enroll');
        $this->Enroll->user_id = $content['user_id'];
        $this->Enroll->stopped = true;
        $enrolls = $this->Enroll->get_index(1000, 0);

        $this->load->model('Rent');
        $this->Rent->user_id = $content['user_id'];
        $this->Rent->stopped = true;
        $rents = $this->Rent->get_index(1000, 0);

        $this->load->model('RentSw');
        $this->RentSw->user_id = $content['user_id'];
        $this->RentSw->stopped = true;
        $rent_sws = $this->RentSw->get_index(1000, 0);
        if ($enrolls['total'] or $rents['total'] or $rent_sws['total']) {
            $this->load->model('OrderStop');
            if ($enrolls['total']) {
                $this->parent_model = 'Enroll';

                foreach ($enrolls['list'] as $enroll) {
                    $order_stop = $this->OrderStop->get_content_by_parent_id($enroll['order_id']);

                    if (empty($order_stop)) {
                        continue;
                    }

                    $data = $this->calculator_stop_data($data, $order_stop);
                    $data['parent_id'] = $enroll['id'];

                    if ($this->resume_process($data, $order_stop)) {
                        parent::resume_after_process($data, $order_stop);
                        //exit;
                    } else {
                        ++$fail_count;
                    }
                }
            }

            if ($rents['total']) {
                $this->parent_model = 'Rent';
                foreach ($rents['list'] as $rent) {
                    $order_stop = $this->OrderStop->get_content_by_parent_id($rent['order_id']);

                    if (empty($order_stop)) {
                        continue;
                    }

                    $data = $this->calculator_stop_data($data, $order_stop);
                    $data['parent_id'] = $rent['id'];

                    if ($this->resume_process($data, $order_stop)) {
                        parent::resume_after_process($data, $order_stop);
                    } else {
                        ++$fail_count;
                    }
                }
            }

            if ($rent_sws['total']) {
                $this->parent_model = 'RentSw';
                foreach ($rent_sws['list'] as $rent_sw) {
                    $order_stop = $this->OrderStop->get_content_by_parent_id($rent_sw['order_id']);

                    if (empty($order_stop)) {
                        continue;
                    }

                    $data = $this->calculator_stop_data($data, $order_stop);
                    $data['parent_id'] = $rent_sw['id'];

                    if ($this->resume_process($data, $order_stop)) {
                        parent::resume_after_process($data, $order_stop);
                    } else {
                        ++$fail_count;
                    }
                }
            }
        }
    }

    protected function set_form_validation($id = null)
    {
        $params = $this->input->post('stop_start_date');
        if ($this->input->post('stop_end_date')) {
            $params .= ',' . $this->input->post('stop_end_date');
        } else {
            $params .= ',';
        }

        if (empty($id)) {
            $this->form_validation->set_rules('user_id', _('User'), 'required|integer|callback_user_stop_valid_date_period[' . $params . ']');
        } else {
            $params .= ',' . $id;
            $this->form_validation->set_rules('user_id', _('User'), 'integer|integer|callback_user_stop_valid_date_period[' . $params . ']');
        }

        if ($this->router->fetch_method() == 'add') {
            $this->form_validation->set_rules('order_id', _('Order'), 'required|integer|callback_valid_enrolls');
            $this->form_validation->set_rules('stop_start_date', _('Stop Start Date'), 'required|callback_valid_date|callback_valid_date_order_start_after[' . $this->input->post('order_id') . ']|callback_valid_date_order_end_before[' . $this->input->post('order_id') . ']');
        }

        $this->form_validation->set_rules('stop_end_date', _('Stop End Date'), 'callback_valid_date|callback_valid_date_after[' . $this->input->post('stop_start_date') . ']');
        $this->form_validation->set_rules('request_date', _('Request Date'), 'callback_valid_date');
    }

    public function valid_enrolls() {
        $enrolls=$this->get_enrolls($this->input->post('user_id'),$this->input->post('stop_start_date'));

        if(empty($enrolls['total'])) {
            return false;
        } else {
            return true;
        }
    }

    public function valid_date_order_start_after($stop_start_date,$order_id)
    {
        $this->load->model('Enroll');
        $content=$this->Enroll->get_content_by_order_id($order_id);

        return $this->valid_date_after($stop_start_date,$content['start_date']);
    }

    public function valid_date_order_end_before($stop_start_date,$order_id)
    {
        $this->load->model('Enroll');
        $content=$this->Enroll->get_content_by_order_id($order_id);

        return $this->valid_date_before($stop_start_date,$content['end_date']);
    }

    // 중지기간 중복되는거 없는지 검사
    public function user_stop_valid_date_period($user_id, $params)
    {
        $param = explode(',', $params);
        $stop_start_date = $param[0];
        $stop_end_date = $param[1];

        $this->load->model('UserStop');

        if (!empty($param[2])) {
            $user_stop_id = $param[2];
            $user_stop_content = $this->UserStop->get_content($user_stop_id);
            $not_id = $user_stop_content['id'];
        }

        $start_new_obj = new DateTime($stop_start_date, $this->timezone);

        if (empty($stop_end_date)) {
            $end_date = $this->max_date;
        } else {
            $end_date = $stop_end_date;
        }

        $end_new_obj = new DateTime($end_date, $this->timezone);

        $this->UserStop->user_id = $user_id;

        if (!empty($not_id)) {
            $this->UserStop->not_id = $not_id;
        }
        $user_stops = $this->UserStop->get_index();

        if (!empty($user_stops['total'])) {
            foreach ($user_stops['list'] as $user_stop) {
                if (isset($user_stop_id)) {
                    if ($user_stop['id'] == $user_stop_id) {
                        continue;
                    }
                }

                $start_order_obj = new DateTime($user_stop['stop_start_date'], $this->timezone);

                if (empty($user_stop['stop_end_date'])) {
                    $end_order_date = $this->max_date;
                } else {
                    $end_order_date = $user_stop['stop_end_date'];
                }
                $end_order_obj = new DateTime($end_order_date, $this->timezone);

                if (($end_new_obj > $start_order_obj) and ($start_new_obj < $end_order_obj)) {
                    return false;
                }
            }
        }

        $this->load->model('UserStopSchedule');
        $this->UserStopSchedule->user_id = $user_id;
        $user_schedule_stops = $this->UserStopSchedule->get_index();

        if (!empty($user_schedule_stops['total'])) {
            foreach ($user_schedule_stops['list'] as $user_schedule_stop) {
                if (isset($user_stop_id)) {
                    if ($user_schedule_stop['user_stop_id'] == $user_stop_id) {
                        continue;
                    }
                }

                $start_order_obj = new DateTime($user_schedule_stop['schedule_date'], $this->timezone);

                if (empty($user_schedule_stop['stop_day_count'])) {
                    $end_order_obj = new DateTime($this->max_date, $this->timezone);
                } else {
                    $end_order_obj = new DateTime($start_order_obj->format('Y-m-d'), $this->timezone);
                    $end_order_obj->modify('+' . $user_schedule_stop['stop_day_count'] . ' Day');
                }

                if (($end_new_obj > $start_order_obj) and ($start_new_obj < $end_order_obj)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function edit_log($id)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('stop_start_date', _('Stop Start Date'), 'required|callback_valid_date');
        $this->form_validation->set_rules('stop_end_date', _('Stop End Date'), 'required|callback_valid_date|callback_valid_date_after[' . $this->input->post('stop_start_date') . ']');
        $this->form_validation->set_rules('request_date', _('Request Date'), 'required|callback_valid_date');

        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($id);

        $this->load->model('OrderStopLog');
        $this->OrderStopLog->user_stop_id = $content['id'];
        $stopped_orders = $this->OrderStopLog->get_index();

        if ($this->form_validation->run() == false) {
            $this->load->model('UserStop');
            $this->UserStop->user_id = $content['id'];
            $user_stops = $this->UserStop->get_index();

            $this->load->model('UserStopSchedule');
            $this->UserStopSchedule->user_id = $content['id'];
            $user_stop_schedules = $this->UserStopSchedule->get_index();

            $this->return_data['data']['content'] = $content;
            $this->return_data['data']['stopped_orders'] = $stopped_orders;
            $this->return_data['data']['user_stops'] = $user_stops;
            $this->return_data['data']['user_stop_schedules'] = $user_stop_schedules;

            $this->script = 'user-stops/edit-log.js';
            $this->render_format();
        } else {
            $data = $this->input->post(null, true);
            $stop_days = $this->get_stop_day_count($content['stop_start_date'], $data['stop_end_date']);
            $this->{$this->model}->update(array('id' => $content['id'], 'stop_end_date' => $data['stop_end_date'], 'stop_day_count' => $stop_days));

            $content_action = false;

            $this->load->model('UserStopContent');
            if (empty(trim($data['content']))) {
                if (!empty($content['content_id'])) {
                    $this->UserStopContent->delete($content['content_id']);
                    $content_action = 'delete';
                }
            } else {
                if (empty($content['content_id'])) {
                    $this->UserStopContent->insert(array('user_stop_id' => $content['id'], 'content' => $data['content']));
                    $content_action = 'insert';
                } else {
                    $this->UserStopContent->update(array('id' => $content['content_id'], 'content' => $data['content']));
                    $content_action = 'update';
                }
            }

            if (!empty($stopped_orders['total'])) {
                $this->load->model('OrderStop');
                $this->load->model('Enroll');
                $this->load->model('Rent');
                $this->load->model('RentSw');

                if (!empty($content_action)) {
                    $this->load->model('OrderStopLogContent');
                }

                foreach ($stopped_orders['list'] as $value) {
                    $origin_end_date = new DateTime($value['origin_end_date'], $this->timezone);
                    $origin_end_date->modify('+' . $stop_days . ' Days');
                    $change_end_date = $origin_end_date->format('Y-m-d');
                    $this->OrderStopLog->update(array('id' => $value['id'], 'stop_end_date' => $data['stop_end_date'], 'stop_day_count' => $stop_days, 'change_end_date' => $change_end_date));
                    $this->OrderStop->update(array('id' => $value['order_stop_id'], 'stop_end_date' => $data['stop_end_date']));

                    if ($enroll_id = $this->Enroll->get_id_by_order_id($value['order_id'])) {
                        $this->Enroll->update(array('id' => $enroll_id, 'end_date' => $change_end_date));
                    }

                    if ($rent_id = $this->Rent->get_id_by_order_id($value['order_id'])) {
                        $this->Rent->update(array('id' => $rent_id, 'end_datetime' => $change_end_date . ' 23:59:59'));
                    }

                    if ($rent_sw_id = $this->RentSw->get_id_by_order_id($value['order_id'])) {
                        $this->RentSw->update(array('id' => $rent_sw_id, 'end_date' => $change_end_date));
                    }

                    if (!empty($content_action)) {
                        switch ($content_action) {
                            case 'delete':
                                $this->OrderStopLogContent->delete($value['content_id']);
                                break;
                            case 'update':
                                $this->OrderStopLogContent->update(array('id' => $value['content_id'], 'content' => $data['content']));
                                break;
                            case 'insert' :
                                $this->OrderStopLogContent->insert(array('order_stop_log_id' => $value['id'], 'content' => $data['content']));
                                break;
                        }
                    }
                }
            }

            if (true) {
                $this->session->set_flashdata('message', ['type' => 'success', 'message' => _('Successfully Created Article')]);
                redirect('/user-stops/edit-log/' . $id);
            } else {
                redirect('/user-stops/edit-log/' . $id);
            }
        }
    }

    protected function insert_complete_message($id)
    {
        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($id);

        if ($this->schedule) {
            return sprintf(_('Successfully Schedule Stop %s`s Order'), $content['name']);
        } else {
            return sprintf(_('Successfully All Stop %s`s Order'), $content['name']);
        }
    }

    protected function add_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            $this->load->model($this->model);
            $content = $this->{$this->model}->get_content($id);

            return '/home/stops/' . $content['user_id'];
        }
    }

    protected function delete_redirect_path(array $content)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            $return_url = '/home/stops/' . $content['user_id'];
            if (!empty($content['uss_exist'])) {
                $return_url .= '?tab=2';
            }

            return $return_url;
        }
    }

    protected function after_delete_data(array $content, $data = null)
    {
        if ($content['enable']) {
            $this->load->model('Order');
            $this->Order->resume_by_user_id($content['user_id']);
        }

        $this->load->model('OrderStopLog');
        $this->OrderStopLog->user_stop_id=$content['id'];
        $order_stop_logs=$this->OrderStopLog->get_index();

        if(empty($order_stop_logs['total'])) {
            return true;
        }

        foreach($order_stop_logs['list'] as $order_stop_log) {
            $order_id=$order_stop_log['order_id'];
            $origin_end_date=$order_stop_log['origin_end_date'];

            $this->load->model('Enroll');
            $enroll=$this->Enroll->get_content_by_order_id($order_id);
            if(!empty($enroll)) {
                $this->Enroll->update(array('end_date'=>$origin_end_date,'id'=>$enroll['id']));
            }
            
            $this->load->model('Rent');
            $rent=$this->Rent->get_content_by_order_id($order_id);
            if(!empty($rent)) {
                $this->Rent->update(array('end_datetime'=>$origin_end_date,'id'=>$rent['id']));
            }

            $this->load->model('RentSw');
            $rent_sw=$this->RentSw->get_content_by_order_id($order_id);
            if(!empty($rent_sw)) {
                $this->RentSw->update(array('end_date'=>$origin_end_date,'id'=>$rent_sw['id']));
            }
        }
    }

    protected function delete_complete_message(array $content)
    {
        if (empty($content['uss_exist'])) {
            return _('Successfully Cancel Stop');
        } else {
            return _('Successfully Cancel Stop Schedule');
        }
    }

    public function delete_confirm($id)
    {
        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($id);

        $this->return_data['data']['id'] = $id;
        $this->return_data['data']['content'] = $content;

        $this->layout->render('/user_stops/delete', $this->return_data);
    }

    protected function get_error_messages()
    {
        $message = parent::get_error_messages();
        $message['user_stop_valid_date_period'] = _('Already Same Period Stop Exists');
        $message['valid_date_order_start_after'] = _('Stop Start Date must great than Start Date');
        $message['valid_date_order_end_before'] = _('Stop Start Date must less than End Date');
        $message['valid_date_after'] = _('Stop End Date must after Stop Start Date');
        return $message;
    }

    private function send_message_and_insert_log(array $send_users,array $message)
    {
        $this->load->model('Message');
        $users = $this->Message->get_push_user($send_users);

        if(empty($users)) {
            return false;
        }

        $data = ['type' => 'push', 'user' => $send_users,'title'=>$message['title'],'content'=>$message['content']];

        $this->send_push($users, $message);

        $message_id = $this->Message->insert($data);
        
        $this->load->model('MessageUser');
        foreach ($send_users as $send_user) {
            $this->MessageUser->insert(['message_id' => $message_id, 'user_id' => $send_user,'title'=>$message['title'],'description'=>$message['content']]);
        }

        $this->load->model('MessageSender');
        return $this->MessageSender->insert(['message_id' => $message_id, 'admin_id' => $this->session->userdata('admin_id')]);        
    }

}
