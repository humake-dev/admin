<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_photo.php';

class Employee_pictures extends SL_photo
{
    protected $model = 'EmployeePicture';
    protected $permission_controller = 'employees';
    protected $directory = 'employee';
    protected $thumb_array = array('large_thumb' => array('width' => 1080, 'height' => 340), 'medium_thumb' => array('width' => 540, 'height' => 175), 'small_thumb' => array('width' => 275, 'height' => 88));

    public function update_photo($id, $redirect = true)
    {
        $this->load->model($this->model);

        try {
            $upload_datas = $this->file_upload();

            foreach ($upload_datas as $upload_data) {
                $upload_file_data = array();
                $upload_file_data['picture_url'] = $upload_data['file_name'];
                $upload_file_data['admin_id'] = $id;

                $inserted_id = $this->{$this->model}->insert($upload_file_data);
            }

            if ($this->format == 'json') {
                if ($this->session->userdata('admin_id') == $id) {
                    $this->session->set_userdata(array('admin_picture' => $upload_file_data['picture_url']));
                }
                echo json_encode(array('result' => 'success', 'id' => $inserted_id, 'photo' => urlencode(getPhotoPath(lcfirst($this->directory), $this->category_directory, $upload_data['file_name'], 'large'))));
            } else {
                if ($redirect) {
                    redirect($this->router->fetch_class() . '?id=' . $id);
                } else {
                    return $upload_file_data['picture_url'];
                }
            }
        } catch (exception $e) {
            $error = $e->getMessage();

            if ($this->format == 'json') {
                echo json_encode(array('result' => 'error'));
            } else {
                echo $error;
            }

            return false;
        }
    }

    protected function delete_complete_message(array $content)
    {
        return _('Successfully Delete Photo');
    }

    protected function delete_redirect_path(array $content)
    {
        return 'employees/view/' . $content['admin_id'];
    }
}
