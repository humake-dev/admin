<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Counsel_responses extends SL_Controller
{
    protected $model = 'CounselResponse';
    protected $permission_controller = 'counsels';
    
    protected function set_add_form_data()
    {
        $this->load->model('Counsel');
        $content=$this->Counsel->get_content($this->input->get_post('counsel_id'));

        $this->return_data['data']['content']=$content;

        $admins=$this->get_admin_list();


            if(!empty($admins['total'])) {
                $managers=array('total'=>0,'list'=>array());

                foreach($admins['list'] as $admin) {
                    if(!empty($admin['is_fc'])) {
                        $managers['total']++;
                        $managers['list'][]=$admin;
                    }
                }

                $this->return_data['data']['manager']=$managers;
            }
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data();
        $this->return_data['data']['content'] = $content;
    }

    protected function add_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return '/counsel-requests';
        }
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('counsel_id', _('Counsel'), 'required|integer');
        $this->form_validation->set_rules('content', _('Content'), 'required|trim');
    }

    protected function after_insert_data($id, $data)
    {
        $counse_id=$data['counsel_id'];

        $this->load->model('CounselEmployee');        
        if (empty($data['manager_id'])) {
            if ($this->CounselEmployee->get_count_by_parent_id($counse_id)) {
                $this->CounselEmployee->delete_by_parent_id($counse_id);
            }
        } else {
            if ($this->CounselEmployee->get_count_by_parent_id($counse_id)) {
                $this->CounselEmployee->update_by_parent_id(['counsel_id' => $counse_id, 'admin_id' => $data['manager_id']]);
            } else {
                $this->CounselEmployee->insert(['counsel_id' => $counse_id, 'admin_id' => $data['manager_id']]);
            }
        }

        $this->load->model('CounselManager');
        if (empty($data['manager_id'])) {
            if ($this->CounselManager->get_count_by_parent_id($counse_id)) {
                $this->CounselManager->delete_by_parent_id($counse_id);
            }
        } else {
            if ($this->CounselManager->get_count_by_parent_id($counse_id)) {
                $this->CounselManager->update_by_parent_id(['counsel_id' => $counse_id, 'admin_id' => $data['manager_id']]);
            } else {
                $this->CounselManager->insert(['counsel_id' => $counse_id, 'admin_id' => $data['manager_id']]);
            }
        }
    }

    protected function get_admin_list($type = 'all', $per_page = 1000, $page = 0, $assign_return_data = true)
    {
        $this->load->model('Employee');
        $role_ids = [3, 4, 5, 6, 7];

        if (in_array($this->session->userdata('role_id'), [1, 2])) {
            $role_ids[] = 2;
        }

        $status = ['H'];
        if ($this->input->get('status')) {
            $get_status = $this->input->get('status');
            if (is_array($get_status)) {
                foreach ($get_status as $g_status) {
                    if (in_array($g_status, ['A'])) {
                        $get_status = ['H', 'R', 'L'];
                        break;
                    }

                    if (in_array($g_status, ['H', 'R', 'L'])) {
                        $get_status[] = $g_status;
                    }
                }
                $status = $get_status;
            }
        }

        if ($this->input->get('employee_name')) {
            $search_word = filter_var($this->input->get('employee_name'), FILTER_SANITIZE_STRING);
        }

        $this->Employee->status = $status;
        $this->Employee->role_ids = $role_ids;

        if (isset($search_word)) {
            $this->Employee->search_word = $search_word;
        }

        $this->Employee->is_fc = false;
        $this->Employee->is_trainer = false;
        switch ($type) {
            case 'fc':
                $this->Employee->is_fc = true;
                break;
            case 'trainer':
                $this->Employee->is_trainer = true;
                break;
        }

        if($this->session->userdata('center_id')) {
            $this->Employee->branch_id=$this->return_data['data']['content']['branch_id'];
        }

        $admin = $this->Employee->get_index($per_page, $page);

        if ($admin['total']) {
            if ($this->input->get('employee_id')) {
                $admin['content'] = $this->Employee->get_content($this->input->get('employee_id'));
            } else {
                $admin['content'] = $admin['list'][0];
            }
        } else {
            $admin['content'] = false;
        }

        if ($assign_return_data) {
            $this->return_data['data']['admin'] = $admin;
            $this->return_data['search_data']['status'] = $status;
        }

        return $admin;
    }
}
