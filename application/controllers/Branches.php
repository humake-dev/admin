<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_photo.php';

class Branches extends SL_photo
{
    protected $model = 'Branch';
    protected $category_model = 'Center';
    protected $category_id_name = 'center_id';
    protected $thumb_array = array('large_thumb' => array('width' => 1080, 'height' => 340), 'medium_thumb' => array('width' => 540, 'height' => 175), 'small_thumb' => array('width' => 275, 'height' => 88));
    protected $file_model ='BranchPicture';

    protected function index_data($category_id = null)
    {
        parent::index_data($category_id);
        $this->return_data['data'] = $this->get_image($this->return_data['data']);
    }

    public function index_oc($type = null)
    {
        if (in_array($type, array('default', 'access'))) {
            $this->session->set_userdata('branch_open', $type);
        } else {
            $this->session->unset_userdata('branch_open');
        }

        echo json_encode(array('result' => 'success'));
    }

    protected function get_image(array $list)
    {
        if (empty($list['total'])) {
            return $list;
        }

        $this->load->model('BranchPicture');
        foreach ($list['list'] as $index => $value) {
            $this->BranchPicture->parent_id = $value['id'];
            $list['list'][$index]['picture'] = $this->BranchPicture->get_index();
        }

        return $list;
    }

    public function view_center()
    {
        $this->session->unset_userdata('branch_id');
        redirect('/');
    }

    public function disable_confirm($id)
    {
        $this->return_data['data']['id'] = $id;
        $this->layout->render('branches/disable-confirm', $this->return_data);
    }

    public function enable_confirm($id)
    {
        $this->return_data['data']['id'] = $id;
        $this->layout->render('branches/enable-confirm', $this->return_data);
    }

    public function enable($id)
    {
        $this->load->model($this->model);
        $this->{$this->model}->enable($id);

        $this->session->set_flashdata('message', array('type' => 'success', 'message' => _('Successfully Enabled Article')));
        redirect('/branches');
    }

    protected function set_add_form_data()
    {
        $this->load->model($this->model);

        if ($this->session->userdata('role_id') == 1) {
            $this->load->model('Center');
            $this->{$this->model}->enable = false;

            $this->return_data['data']['center'] = $this->Center->get_index(1000, 0);
        }

        $this->script = 'branches/add.js';
    }

    protected function set_insert_data($data)
    {
        if ($this->session->userdata('role_id') != 1) {
            $data['center_id'] = $this->session->userdata('center_id');
        }

        return $data;
    }

    protected function set_edit_form_data(array $content)
    {
        $this->set_add_form_data();

        $this->return_data['data']['content'] = $content;

        $this->script = 'branches/add.js';
    }

    protected function set_update_data($id, $data)
    {
        $data = $this->set_insert_data($data);
        $data['id'] = $id;

        return $data;
    }

    protected function after_insert_data($id, $data)
    {
        parent::after_insert_data($id, $data);
        $this->update_category_count($data);

        if (!$this->session->userdata('role_id') == 1) {
            return true;
        }
    }

    protected function after_update_data($id, $data)
    {
        parent::after_update_data($id, $data);
        $this->update_category_count($data);
    }

    protected function file_insert($id)
    {
        if ($this->session->userdata('center_id')) {
            $this->category_directory = $id;
        } else {
            $this->category_directory = $this->session->userdata('branch_id');
        }

        return parent::file_insert($id);
    }

    protected function after_delete_data(array $content, $data = null)
    {
        $this->update_category_count(array('center_id' => $content['center_id']));
    }

    protected function update_category_count($data)
    {
        $this->load->model($this->model);
        $this->{$this->model}->center_id = $data['center_id'];
        $count = $this->{$this->model}->get_count();

        $this->load->model('Center');
        $this->Center->update(array('branch_counts' => $count, 'id' => $data['center_id']));
    }

    protected function delete_complete_message(array $content)
    {
        if ($content['enable']) {
            return _('Successfully Disabled Article');
        } else {
            return parent::delete_complete_message($content);
        }
    }

    public function validate_clients($clients)
    {
        if (empty($clients)) {
            return true;
        }

        if (!is_array($clients)) {
            return false;
        }

        foreach ($clients as $client) {
        }

        return true;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Title'), 'required|trim');
    }
}
