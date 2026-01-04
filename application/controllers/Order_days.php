<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Order_days extends SL_Controller
{
    protected $model = 'OrderDay';
    protected $permission_controller = 'accounts';

    protected function index_data($category_id = null)
    {
        $this->set_page();

        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->form_validation->set_rules('date', _('Date'), 'callback_valid_date');

        $this->load->model($this->model);
        $this->{$this->model}->start_date = $this->date;
        $this->{$this->model}->end_date = $this->date;

        $this->return_data['data'] = $this->{$this->model}->get_index($this->per_page, $this->page,'a.id');

        if ($this->return_data['data']['total']) {
            $this->return_data['data']['sum'] = $this->{$this->model}->get_total()[0]['total'];
        } else {
            $this->return_data['data']['sum'] = 0;
        }

        $this->setting_pagination(array('total_rows' => $this->return_data['data']['total']));
    }
}
