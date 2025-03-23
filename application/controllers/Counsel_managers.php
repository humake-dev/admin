<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Search_period.php';
require_once 'Validate_person.php';

class Counsel_managers extends SL_Controller
{
    use Search_period;

    protected $model = 'CounselManager';
    protected $permission_controller = 'counsels';

    public function add_all()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('counsel', _('Counsel'), 'integer');
        $this->set_message();

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
                $this->set_edit_form_data();
                $this->render_format();
            } else {
                echo json_encode(['result' => 'error', 'message' => validation_errors()]);
            }
        } else {
            $data = $this->input->post(null, true);
            $search_data=($this->input->get(null, true));

            if (!empty($data['manager'])) {
                $data['admin_id'] = $data['manager'];
            }


            $this->load->model('Counsel');


            if (count($search_data)) {
                $s_search_data = $search_data;

                if (isset($s_search_data['page'])) {
                    unset($s_search_data['page']);
                }

                if (count($s_search_data)) {
                    $this->Counsel->search = $s_search_data;
                    $this->set_search();
                }
            }

            $list=$this->Counsel->get_index(10000);

            if(empty($list['total'])) {
                redirect($data('return_url'));
            }

            $this->load->model(($this->model));

            foreach($list['list'] as $value) {
                $this->{$this->model}->insert(array('admin_id'=>$data['admin_id'],'counsel_id'=>$value['id']));
            }
        }
    }

    protected function set_insert_data($data = null)
    {
        if (!empty($data['counsel'])) {
            $data['counsel_id'] = $data['counsel'];
        }

        if (!empty($data['manager'])) {
            $data['admin_id'] = $data['manager'];
        }

        return $data;
    }

    public function edit($id = null)
    {
        $this->load->library('form_validation');
        $this->set_form_validation($id);
        $this->set_message();

        $before_content = $this->get_view_content_data($id);

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
                $this->set_edit_form_data($before_content);
                $this->render_format();
            } else {
                echo json_encode(['result' => 'error', 'message' => validation_errors()]);
            }
        } else {
            $data = $this->input->post(null, true);
            $data['edit_content'] = $before_content;

            $data = $this->set_update_data($id, $data);

            if (empty($data['admin_id'])) {
                $this->load->model('CounselManager');
                if ($this->CounselManager->get_count($id)) {
                    $this->CounselManager->delete($id);
                }

                if ($this->format == 'json') {
                    echo json_encode(['result' => 'success', 'message' => $this->update_complete_message($id), 'redirect_path' => $this->edit_redirect_path($id)]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'success', 'message' => $this->update_complete_message($id)]);
                    redirect($this->edit_redirect_path($id));
                }
            } else {

            if ($this->{$this->model}->update($data)) {
                $this->after_update_data($id, $data);

                if ($this->format == 'json') {
                    echo json_encode(['result' => 'success', 'message' => $this->update_complete_message($id), 'redirect_path' => $this->edit_redirect_path($id)]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'success', 'message' => $this->update_complete_message($id)]);
                    redirect($this->edit_redirect_path($id));
                }

            } else {
                if ($this->format == 'json') {
                    echo json_encode(['result' => 'error', 'message' => _('Update Fail')]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'danger', 'message' => _('Update Fail')]);
                    redirect($this->router->fetch_class() . '/edit/' . $id);
                }
            }
            }
        }
    }

    protected function set_update_data($id, $data)
    {
        $data = $this->set_insert_data($data);
        $data['id'] = $id;

        return $data;
    }

    protected function after_update_data($id, $data)
    {
        $this->load->model($this->model);
        if (empty($data['admin_id'])) {
            if ($this->{$this->model}->get_count_by_parent_id($id)) {
                $this->{$this->model}->delete_by_parent_id($id);
            }
        }
    }
    
    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('counsel', _('Counsel'), 'integer');
        $this->form_validation->set_rules('manager', _('Manager'), 'integer');
    }
}
