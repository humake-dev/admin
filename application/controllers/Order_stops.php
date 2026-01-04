<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Order_stops extends SL_Controller
{
    protected $parent_model = 'Order';
    protected $model = 'OrderStop';
    protected $permission_controller = 'orders';
    protected $script = 'order-stops/add.js';
    protected $schedule = false;

    protected function set_add_form_data()
    {
        $parent_id = $this->uri->segment(3);

        $this->load->model($this->parent_model);
        $content = $this->{$this->parent_model}->get_content($parent_id);

        $this->return_data['data']['content'] = $content;
        $this->return_data['data']['form_url'] = '/' . strtolower($this->parent_model) . 's/stop/' . $content['id'];
    }

    protected function set_edit_form_data(array $content)
    {
        $this->load->model($this->parent_model);
        $this->{$this->parent_model}->stopped = true;
        $order_content = $this->{$this->parent_model}->get_content($content['order_id']);

        $this->load->model('OrderStopSchedule');
        $content['is_schedule'] = $this->OrderStopSchedule->get_count_by_parent_id($content['id']);

        $this->script = 'order-stops/edit.js';
        $this->return_data['data']['content'] = $content;
        $this->return_data['data']['order_content'] = $order_content;
    }

    protected function after_insert_data($id, $data)
    {
        $this->load->model('Order');
        $this->Order->update(array('id' => $data['order_id'], 'stopped' => 1));
    }

    protected function after_update_data($id, $data)
    {
        $this->load->model('Enroll');
        $this->Enroll->order_id = $data['edit_content']['order_id'];
        $enrolls = $this->Enroll->get_index();

        if (empty($enrolls['total'])) {
            return true;
        }

        $enroll_content = $enrolls['list'][0];

        $this->load->model('Rent');
        $this->Rent->user_id = $data['edit_content']['user_id'];
        $this->Rent->stopped = true;
        $this->Rent->start_date = $enroll_content['start_date'];
        $this->Rent->end_date = $enroll_content['end_date'];
        $rents = $this->Rent->get_index();

        $this->load->model('RentSw');
        $this->RentSw->user_id = $data['edit_content']['user_id'];
        $this->RentSw->stopped = true;
        $this->RentSw->start_date = $enroll_content['start_date'];
        $this->RentSw->end_date = $enroll_content['end_date'];
        $rent_sws = $this->RentSw->get_index();

        if (empty($rents['total']) and empty($rent_sws['total'])) {
            return true;
        }

        $this->load->model($this->model);

        if (!empty($rents['total'])) {
            foreach ($rents['list'] as $rent) {
                $this->{$this->model}->order_id = $rent['order_id'];
                $order_stops = $this->{$this->model}->get_index();

                if (empty($order_stops['total'])) {
                    continue;
                }

                foreach ($order_stops['list'] as $order_stop) {
                    $data['id'] = $order_stop['id'];
                    $this->{$this->model}->update($data);
                }
            }
        }

        if (!empty($rent_sws['total'])) {
            foreach ($rent_sws['list'] as $rent_sw) {
                $this->{$this->model}->order_id = $rent_sw['order_id'];
                $order_stops = $this->{$this->model}->get_index();

                if (empty($order_stops['total'])) {
                    continue;
                }

                foreach ($order_stops['list'] as $order_stop) {
                    $data['id'] = $order_stop['id'];
                    $this->{$this->model}->update($data);
                }
            }
        }
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
        }

        $this->load->model($this->parent_model);
        $p_content = $this->{$this->parent_model}->get_content($parent_id);

        $stop_data = $this->calculator_stop_data($data, $p_content);

        $data['order_id'] = $p_content['order_id'];
        $data['user_id'] = $p_content['user_id'];

        return array_merge($data, $stop_data);
    }

    protected function set_update_data($id, $data)
    {
        if (empty($data['stop_end_date'])) {
            $data['stop_end_date'] = null;
        }

        $stop_data = $this->calculator_stop_data($data, $data['edit_content']);
        $return_data = array_merge($data, $stop_data);
        $return_data['id'] = $id;

        return $return_data;
    }

    protected function calculator_stop_data(array $data, array $content)
    {
        if (empty($data['stop_start_date']) and empty($content['stop_start_date'])) {
            throw new Exception('start_date not insert');
        }

        if (empty($data['stop_end_date']) and empty($content['stop_end_date'])) {
            //throw new Exception('end_date not insert');
            return $data;
        }

        if (!empty($content['stop_start_date'])) {
            $stop_start_date_obj = new DateTime($content['stop_start_date'], $this->timezone);
        }

        if (!empty($content['stop_end_date'])) {
            $stop_end_date_obj = new DateTime($content['stop_end_date'], $this->timezone);
        }

        if (!empty($data['stop_start_date'])) {
            $stop_start_date_obj = new DateTime($data['stop_start_date'], $this->timezone);
        }

        if (!empty($data['stop_end_date'])) {
            $stop_end_date_obj = new DateTime($data['stop_end_date'], $this->timezone);
        }

        $start_date_obj = new DateTime($content['start_date'], $this->timezone);
        $end_date_obj = new DateTime($content['end_date'], $this->timezone);

        $stop_day_count = 0;
        $is_change_start_date = false;

        if ($content['end_date'] == $this->max_date) {
            $change_end_date = $content['end_date'];
        } else {
            if (empty($data['stop_end_date'])) {
                $data['stop_end_date'] = null;
            } else {
                $interval_day_count = $stop_start_date_obj->diff($stop_end_date_obj);
                $stop_day_count = intval($interval_day_count->format('%a')) + 1;

                // 휴회기간이 시작일 이후면
                if ($start_date_obj < $stop_start_date_obj) {
                    if($end_date_obj > $stop_start_date_obj) {
                        // 휴회기간이 휴회신청기간
                        $plus_day_count = $stop_day_count;
                    } else {
                        $plus_day_count=0;
                    }
                } else {
                    if ($start_date_obj < $stop_end_date_obj) {
                        $interval_day_count = $start_date_obj->diff($stop_end_date_obj);
                        $plus_day_count = intval($interval_day_count->format('%a')) + 1;
                    } else {
                        // 휴회기간이 휴회신청기간
                        $plus_day_count = $stop_day_count;
                    }
                }

                // 휴회기간을 넘지 않게
                if($plus_day_count>$stop_day_count) {
                    $plus_day_count=$stop_day_count;
                }

                if ($stop_start_date_obj < $start_date_obj) {
                    $is_change_start_date = true;
                }

                $end_date_obj->modify('+' . $plus_day_count . ' Day');
                $change_end_date = $end_date_obj->format('Y-m-d');
            }
        }

        $data['stop_day_count'] = $stop_day_count;
        $data['change_end_date'] = $change_end_date;

        if (!empty($is_change_start_date)) {
            $start_date_obj->modify('+' . $plus_day_count . ' Day');
            $data['change_start_date'] = $start_date_obj->format('Y-m-d');
        }

        $data['is_change_start_date'] = $is_change_start_date;

        return $data;
    }

    public function resume($id)
    {
        $this->load->library('form_validation');
        $this->set_message();

        $this->load->model($this->parent_model);
        $p_content = $this->{$this->parent_model}->get_content($id);

        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content_by_parent_id($p_content['order_id']);

        $this->form_validation->set_rules('order_stop_id', _('Order Stop'), 'required|integer');
        $this->form_validation->set_rules('stop_end_date', _('Stop End Date'), 'required|callback_valid_date|callback_valid_date_after[' . $content['stop_start_date'] . ']|callback_valid_date_before');

        if ($this->form_validation->run() == false) {
            if (empty($this->resume_url)) {
                $this->return_data['data']['form_url'] = '/' . strtolower($this->parent_model) . 's/resume/' . $p_content['id'];
            } else {
                $this->return_data['data']['form_url'] = '/' . $this->resume_url . '/' . $p_content['id'];
            }
            $this->return_data['data']['parent_content'] = $p_content;
            $this->return_data['data']['content'] = $content;

            $this->default_view_file = 'resume.php';
            $this->script = 'order-stops/resume.js';
            $this->render_format();
        } else {
            $data = $this->input->post(null, true);
            $data['stop_start_date'] = $content['stop_start_date'];
            $data['stop_end_date'] = $content['stop_end_date'];

            $data = $this->calculator_stop_data($data, $content);

            if ($this->resume_process($data, $content)) {
                $this->resume_after_process($data, $content);

                $this->session->set_flashdata('message', array('type' => 'success', 'message' => _('Successfully Resume Order')));
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect('/orders/resume/' . $p_content['id']);
            }
        }
    }

    protected function resume_process(array $data, array $content)
    {
        $this->load->model('Order');
        $this->Order->update(array('id' => $content['order_id'], 'stopped' => 0));

        $this->load->model('OrderStop');
        $this->OrderStop->update(array('id' => $content['id'], 'stop_day_count' => $data['stop_day_count'], 'enable' => 0, 'stop_end_date' => $data['stop_end_date'], 'is_change_start_date' => $data['is_change_start_date']));

        if (($data['stop_end_date'] == $content['stop_start_date']) and ($data['stop_end_date'] == $this->today)) {
            return true;
        }

        if (empty($data['is_change_start_date'])) {
            $update_array = array('id' => $data['parent_id'], 'end_date' => $data['change_end_date']);
        } else {
            $update_array = array('id' => $data['parent_id'], 'start_date' => $data['change_start_date'], 'end_date' => $data['change_end_date']);
        }
        $this->load->model($this->parent_model);

        return $this->{$this->parent_model}->update($update_array);
    }

    protected function resume_after_process(array $data, array $content)
    {
        $insert_stop_log = true;

        if (($data['stop_end_date'] == $content['stop_start_date']) and ($data['stop_end_date'] == $this->today)) {
            $this->OrderStop->delete($content['id']);
            $insert_stop_log = false;
        } else {
            $this->OrderStop->update(array('id' => $content['id'], 'enable' => 0, 'updated_at' => $this->now));
        }

        if ($insert_stop_log) {
            $this->load->model('OrderStopLog');
            $order_stop_log_id = $this->OrderStopLog->insert(array('user_id' => $content['user_id'], 'order_id' => $content['order_id'], 'stop_day_count' => $data['stop_day_count'], 'stop_start_date' => $content['stop_start_date'], 'stop_end_date' => $data['stop_end_date'], 'origin_end_date' => $content['end_date'], 'change_end_date' => $data['change_end_date'], 'request_date' => $content['request_date']));

            if (!empty($content['content'])) {
                $this->load->model('OrderStopLogContent');
                $this->OrderStopLogContent->insert(array('order_stop_log_id' => $order_stop_log_id, 'content' => $content['content']));
            }

            $this->load->model('OrderStopLogOrderStop');
            $this->OrderStopLogOrderStop->insert(array('order_stop_log_id' => $order_stop_log_id, 'order_stop_id' => $content['id']));
        }
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('stop_start_date', _('Stop Start Date'), 'required|callback_valid_date');
        //$this->form_validation->set_rules('stop_day_count', _('Stop Day Count'), 'integer');

        if ($this->input->post('stop_end_date')) {
            $this->form_validation->set_rules('stop_end_date', _('Stop End Date'), 'callback_valid_date|callback_valid_date_after[' . $this->input->post('stop_start_date') . ']');
        }
    }

    // 최소 7일이상 신청
    public function valid_date_period($end_date, $start_date)
    {
        $beforeObj = new DateTime($start_date, $this->timezone);
        $afterObj = new DateTime($end_date, $this->timezone);
        $diff = $beforeObj->diff($afterObj);

        if ($diff->format('%a') < 6) {
            $beforeObj->modify('+6Days');
            $available_date = $beforeObj->format('Y' . _('Year') . ' n' . _('Month') . ' j' . _('Day'));

            $this->form_validation->set_message('valid_date_period', sprintf(_('The Stop End Date must greater than or same +7Day(%s)'), $available_date));

            return false;
        }

        return true;
    }

    protected function get_error_messages()
    {
        $message = parent::get_error_messages();
        $message['order_valid_date_period'] = _('Already Same Period Stop Exists');

        return $message;
    }

    protected function edit_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return $_SERVER['HTTP_REFERER'];
        }
    }

    protected function delete_redirect_path(array $content)
    {
        return '/home/stops/' . $content['user_id'];
    }
}
