<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Message_prepares extends SL_Controller
{
    protected $model = 'MessagePrepare';
    protected $permission_controller = 'messages';
    protected $script = 'message-prepares/select.js';

    protected function set_add_form_data()
    {
        $this->index_data();
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data();
        $this->return_data['data']['content'] = $content;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Title'), 'required|min_length[3]|max_length[60]');
        $this->form_validation->set_rules('content', _('Content'), 'required|min_length[3]');
    }

    protected function after_insert_data($id, $data)
    {
        $this->load->model('MessagePrepareAdmin');
        $this->MessagePrepareAdmin->insert(array('message_prepare_id' => $id, 'admin_id' => $this->session->userdata('admin_id')));
    }

    public function select()
    {
        $this->load->model($this->model);
        $this->set_page();

        $list = $this->{$this->model}
            ->get_index($this->per_page, $this->page);
        $this->return_data['data'] = $list;

        if ($this->format == 'json') {
            if ($this->return_data['data']['total']) {
                echo json_encode(array('result' => 'success', 'total' => $this->return_data['data']['total'], 'list' => $this->return_data['data']['list']));
            } else {
                echo json_encode(array('result' => 'success', 'total' => $this->return_data['data']['total']));
            }
        } else {
            $this->setting_pagination(array('total_rows' => $this->return_data['data']['total']));
            $this->return_data['data']['per_page'] = $this->per_page;
            $this->return_data['data']['page'] = $this->page;
            $this->script = 'message-prepares/select.js';
            $this->render_format();
        }
    }
}
