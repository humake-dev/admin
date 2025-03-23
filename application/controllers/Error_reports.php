<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_file.php';

class Error_reports extends SL_file
{
    protected $model = 'ErrorReport';
    protected $file_model = 'ErrorReportFile';
    protected $permission_controller = 'users';

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);
        $this->set_page();

        $branch_ids=$this->get_branch_ids();

        $this->{$this->model}->branch_id=$branch_ids;
        $list = $this->{$this->model}->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->return_data['data'] = $this->get_file($this->return_data['data']);
    }

    protected function get_view_data($id)
    {
        $content=$this->get_view_content_data($id);

        $this->load->model('ErrorReportFile');
        $this->ErrorReportFile->parent_id = $content['id'];
        $content['file'] = $this->ErrorReportFile->get_index();

        return $content;
    }

    protected function set_update_data($id, $data)
    {
        $data['id'] = $id;

        if ($this->session->userdata('role_id') < 4) {
            if (empty($data['solve'])) {
                $data['solve'] = 0;
                $data['solve_date'] = null;
            } else {
                $data['solve_date'] = $this->today;
            }
        }

        return $data;
    }

    protected function get_file(array $list)
    {
        if (empty($list['total'])) {
            return $list;
        }
        
        $this->load->model('ErrorReportFile');
        foreach ($list['list'] as $index => $value) {
            $this->ErrorReportFile->parent_id = $value['id'];
            $list['list'][$index]['file'] = $this->ErrorReportFile->get_index();
        }

        return $list;
    }
    
    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Title'), 'required|min_length[3]|max_length[60]');
        $this->form_validation->set_rules('content', _('Content'), 'required|trim');
    }
}
