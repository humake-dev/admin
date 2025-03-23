<?php

trait Search_period
{
    protected function set_period_search_validation()
    {
        $this->form_validation->set_rules('date_p', _('date_p'), 'in_list[0,7,30,90,180,365,all]');

        if ($this->input->get('start_date')) {
            $this->form_validation->set_rules('start_date', _('Start Date'), 'callback_valid_date');
        }

        if ($this->input->get('end_date')) {
            $this->form_validation->set_rules('end_date', _('End Date'), 'callback_valid_date|callback_valid_search_date[' . $this->input->get('start_date') . ']');
        }
    }

    protected function set_search($model = null, $date_p = 'all', $future = false)
    {
        if (empty($model)) {
            $model = $this->model;
        }

        $this->load->model($model);

        if ($this->input->get('date_p')) {
            $date_p = $this->input->get('date_p');
        }


        if ($this->input->get('start_date')) {
            $start_date = $this->input->get('start_date');
        } else {
            if ($date_p == 'all') {
                $start_date = $this->min_date;
            } else {
                if ($future) {
                    $start_date = $this->today;
                } else {
                    $default_start_date_obj = new DateTime('now', $this->timezone);
                    $default_start_date_obj->modify('Previous Month');
                    $start_date = $default_start_date_obj->format('Y-m-d');
                }
            }
        }

        $display_start_date = '';
        if ($start_date != $this->min_date) {
            $display_start_date = $start_date;
        }

        if ($this->input->get('end_date')) {
            $end_date = $this->input->get('end_date');
        } else {
            if ($future) {
                $default_end_date_obj = new DateTime('now', $this->timezone);
                $default_end_date_obj->modify('Next Month');
                $end_date = $default_end_date_obj->format('Y-m-d');
            } else {
                $end_date = $this->max_date;
            }
        }

        $display_end_date = '';
        if ($end_date != $this->max_date) {
            $display_end_date = $end_date;
        }
        
        if (!$this->input->get('no_period_search')) {
            $this->{$model}->start_date = $start_date;
            $this->{$model}->end_date = $end_date;
        }

        if ($this->session->userdata('show_omu')) {
            if ($this->session->userdata('is_trainer')) {
                $this->{$model}->trainer_id = $this->session->userdata('admin_id');
            }

            if ($this->session->userdata('is_fc')) {
                $this->{$model}->fc_id = $this->session->userdata('admin_id');
            }
        } else {
            if ($this->input->get('trainer')) {
                $this->{$model}->trainer_id = $this->input->get('trainer');
            } else {
                if ($this->input->get('trainer')=='0') {
                    $this->{$model}->search_trainer_null=true;
                }
            }

            if ($this->input->get('fc')) {
                $this->{$model}->fc_id = $this->input->get('fc');
            } else {
                if ($this->input->get('fc')=='0') {
                    $this->{$model}->search_fc_null=true;
                }
            }
        }

        $this->return_data['search_data']['date_p'] = $date_p;
        $this->return_data['search_data']['display_start_date'] = $display_start_date;
        $this->return_data['search_data']['display_end_date'] = $display_end_date;
    }

    public function valid_search_date($after_date=null, $before_date=null)
    {
        if(!empty($after_date) and !empty($before_date)) {
            $beforeObj = new DateTime($before_date, $this->timezone);
            $afterObj = new DateTime($after_date, $this->timezone);
            if ($beforeObj>$afterObj) {
                return false;
            }
        }

        return true;
    }  
}
