<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Facility_breakdowns extends SL_Controller
{
    protected $model = 'FacilityBreakdown';
    protected $permission_controller = 'rents';

    public function set_delete_form_validation()
    {
        $this->form_validation->set_rules('description', _('Description'), 'trim');
    }

    protected function set_add_form_data()
    {
        $this->load->model('Facility');
        $this->return_data['data']['content'] = $this->Facility->get_content($this->input->get('facility_id'));

        if ($this->input->get('no')) {
            $this->return_data['data']['no'] = $this->input->get('no');
        }
    }

    protected function set_edit_form_data($content)
    {
        parent::set_edit_form_data($content);

        if ($this->input->get('no')) {
            $this->return_data['data']['no'] = $this->input->get('no');
        }
    }

    public function delete_confirm($id)
    {
        $this->return_data['data']['id'] = $id;
        $this->layout->render('facility_breakdowns/delete', $this->return_data);
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('facility_id', _('Facility'), 'required|integer');
        $this->form_validation->set_rules('no', _('Facility No'), 'required|integer');
    }

    protected function insert_complete_message($id)
    {
        return _('Successfully Add Breakdown Facility');
    }

    protected function delete_complete_message(array $content)
    {
        return _('Successfully Delete Breakdown Facility');
    }

    protected function add_redirect_path($id)
    {
        return $_SERVER['HTTP_REFERER'];
    }

    protected function delete_redirect_path(array $content)
    {
        return $_SERVER['HTTP_REFERER'];
    }
}
