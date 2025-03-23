<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Enroll_pts extends SL_Controller
{
    protected $model = 'EnrollPt';
    protected $permission_controller = 'employees';
    protected $script = 'enroll-pts/index.js';

    protected function index_data($category_id = null)
    {
        $this->set_search_form_validation();
        $this->set_message();

        $this->load->model($this->model);
        $this->set_page();

        if ($this->input->get('branch_id')) {
            $this->{$this->model}->branch_id = $this->input->get('branch_id');
        }

        switch ($this->input->get('search_type')) {
            case 'search_serial':
                $this->{$this->model}->search_serial = $this->input->get('serial');
                break;
            case 'search_s_or_g':
                $order = 'ept.serial';
                if ($this->input->get('start_serial')) {
                    $this->{$this->model}->start_serial = $this->input->get('start_serial');
                }

                if ($this->input->get('end_serial')) {
                    $this->{$this->model}->end_serial = $this->input->get('end_serial');
                }
                break;
            default:
                $order = 'o.transaction_date';
                $this->{$this->model}->search_period_type = $this->input->get('search_period_type');
                if ($this->input->get('start_date')) {
                    $order = 'e.start_date';
                    $this->{$this->model}->start_date = $this->input->get('start_date');
                }

                if ($this->input->get('end_date')) {
                    $order = 'e.end_date';
                    $this->{$this->model}->end_date = $this->input->get('end_date');
                }
        }
        $order = 'ept.serial';
        $list = $this->{$this->model}->get_index($this->per_page, $this->page, $order);
        $this->return_data['data'] = $list;

        $this->load->model('Branch');
        $this->return_data['data']['branch_list'] = $this->Branch->get_index(10000, 0);

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->form_validation->run();
    }

    protected function set_search_form_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->form_validation->set_rules('branch_id', _('Branch'), 'integer');
        $this->form_validation->set_rules('search_type', _('Search Type'), 'in_list[search_period,search_s_or_g,search_serial]');
        $this->form_validation->set_rules('search_period_type', _('Search Period Type'), 'in_list[start_date,end_date,transaction_date,create_date]');

        switch ($this->input->get('search_type')) {
            case 'search_serial':
                $this->form_validation->set_rules('serial', _('PT Serial'), 'required|integer');
                break;
            case 'search_s_or_g':
                $this->form_validation->set_rules('start_serial', _('Start Serial'), 'integer');
                $this->form_validation->set_rules('end_serial', _('End Serial'), 'integer');
                break;
            default:
                if ($this->input->post('start_date') and $this->input->post('end_date')) {
                    $this->form_validation->set_rules('start_date', _('Start Date'), 'callback_valid_date');
                    $this->form_validation->set_rules('end_date', _('End Date'), 'callback_valid_date|callback_valid_date_after[' . $this->input->post('start_date') . ']');
                } else {
                    $this->form_validation->set_rules('start_date', _('Start Date'), 'callback_valid_date');
                    $this->form_validation->set_rules('end_date', _('End Date'), 'callback_valid_date');
                }
        }
    }

    protected function edit_redirect_path($id)
    {
        return 'enroll-pts';
    }
}
