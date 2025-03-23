<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_photo.php';

class Centers extends SL_photo
{
    protected $model = 'Center';
    protected $thumb_array = array('large_thumb' => array('width' => 1080, 'height' => 340), 'medium_thumb' => array('width' => 540, 'height' => 175), 'small_thumb' => array('width' => 275, 'height' => 88));
    protected $file_model ='CenterPicture';

    protected function index_data($category_id = null)
    {
        parent::index_data($category_id);
        $this->return_data['data'] = $this->get_image($this->return_data['data']);
    }

    protected function get_image(array $list)
    {
        if (empty($list['total'])) {
            return $list;
        }

        if ($list['total']) {
            $this->load->model('CenterPicture');
            foreach ($list['list'] as $index => $value) {
                $this->CenterPicture->parent_id = $value['id'];
                $list['list'][$index]['picture'] = $this->CenterPicture->get_index();
            }
        }

        return $list;
    }

    public function disable_confirm($id)
    {
        $this->return_data['data']['id'] = $id;
        $this->layout->render('centers/disable-confirm', $this->return_data);
    }

    public function enable_confirm($id)
    {
        $this->return_data['data']['id'] = $id;
        $this->layout->render('centers/enable-confirm', $this->return_data);
    }

    public function enable($id)
    {
        $this->load->model($this->model);
        $this->{$this->model}->enable($id);

        $this->session->set_flashdata('message', array('type' => 'success', 'message' => _('Successfully Enabled Article')));
        redirect('/centers');
    }

    protected function set_edit_form_data(array $content)
    {
        $this->set_add_form_data();
        $this->return_data['data']['content'] = $content;
    }

    protected function file_insert($id)
    {
        if ($this->session->userdata('role_id') == 1) {
            $this->category_directory = $id;
        } else {
            $this->category_directory = $this->session->userdata('center_id');
        }

        return parent::file_insert($id);
    }

    protected function delete_complete_message(array $content)
    {
        if ($content['enable']) {
            return _('Successfully Disabled Article');
        } else {
            return parent::delete_complete_message($content);
        }
    }

    public function unique_title($title, $id = null)
    {
        $this->load->model($this->model);
        $this->{$this->model}->title = $title;

        if (!empty($id)) {
            $this->{$this->model}->not_id = $id;
        }

        if ($this->{$this->model}->get_count()) {
            return false;
        }

        return true;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Title'), 'required|trim|callback_unique_title[' . $id . ']');
    }

    protected function get_error_messages()
    {
        $message = parent::get_error_messages();
        $message['unique_title'] = _('The %s field must contain a unique value.');

        return $message;
    }
}
