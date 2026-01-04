<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';

class User_stop_requests extends SL_Controller
{
    use Search_period;

    protected $model = 'UserStopRequest';
    protected $permission_controller = 'users';
    protected $script = 'user-stop-requests/index.js';

    protected function index_data($category_id = null)
    {
        $this->load->helper('text');

        $this->set_page();
        $this->set_search_form_validation();

        $search_type = 'default';
        $search = false;

        $this->load->model($this->model);
        $branch_ids=$this->get_branch_ids();
        $this->{$this->model}->branch_id=$branch_ids;

        if ($this->use_index_content) {
            $this->{$this->model}->get_content = true;
        }

        if ($this->input->get('search_type')) {
            if ($this->input->get('search_type') == 'field') {
                $this->session->set_userdata('counsel_request_search_open', 'field');
                $this->{$this->model}->search_type = 'field';
            } else {
                $this->session->set_userdata('counsel_request_search_open', 'default');
            }
        }

        if ($this->form_validation->run() == false) {
            $this->return_data['search_data']['period_display'] = true;
            $data = $this->{$this->model}->get_index($this->per_page, $this->page);
        } else {
            $search_data = $this->input->get();

            if (count($search_data)) {
                $s_search_data = $search_data;
                if (isset($s_search_data['page'])) {
                    unset($s_search_data['page']);
                }

                if (count($s_search_data)) {
                    $this->{$this->model}->search = $s_search_data;
                    $this->set_search();
                }
            }

            if($this->session->userdata('center_id')) {
                if(!empty($search_data['branch_id'])) {
                    $this->{$this->model}->branch_id=$search_data['branch_id'];
                }
            }
            
            $data = $this->{$this->model}->get_index($this->per_page, $this->page);

            $search = true;
        }

        $this->return_data['data'] = $data;

        if($this->session->userdata('center_id')) {
            $this->load->model('Branch');
            $branch_ids=$this->get_branch_ids();
            $this->Branch->id=$branch_ids;
            $this->return_data['data']['branch_list'] = $this->Branch->get_index(1000, 0);
        }

        $this->return_data['data']['manager'] = $this->get_admin_list('fc');

        $this->return_data['search_data']['search_type'] = $search_type;
        $this->return_data['search_data']['search'] = $search;

        $this->setting_pagination(['total_rows' => $data['total']]);
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    public function index_oc($type = null)
    {
        if (in_array($type, ['default', 'field'])) {
            $this->session->set_userdata('user_stop_request_search_open', $type);
        } else {
            $this->session->unset_userdata('user_stop_request_search_open');
        }

        echo json_encode(['result' => 'success']);
    }
    
    protected function set_add_form_data()
    {
        $this->index_data();
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data();
        $this->return_data['data']['content'] = $content;
    }

        protected function set_search_form_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->set_period_search_validation();
        $this->form_validation->set_rules('manager', _('Manager'), 'integer');
        $this->form_validation->set_rules('complete', _('Counsel Result'), 'integer');
        $this->form_validation->set_rules('search_field', _('Search Field'), 'in_list[name,phone]');
        $this->form_validation->set_rules('search_word', _('Search Word'), 'min_length[1]|trim|max_length[20]');

        if($this->session->userdata('center_id')) {
            $this->form_validation->set_rules('branch_id', _('Branch'), 'integer');
        }
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Title'), 'required|min_length[3]|max_length[60]');
        $this->form_validation->set_rules('content', _('Content'), 'required|trim');
    }
}
