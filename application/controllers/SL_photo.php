<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_file.php';

class SL_photo extends SL_file
{
    protected $max_width = 2560;
    protected $max_height = 2048;
    protected $upload_filename = 'photo';
    protected $thumb_array = array('large_thumb' => array('width' => 400, 'height' => 300), 'medium_thumb' => array('width' => 200, 'height' => 200), 'small_thumb' => array('width' => 100, 'height' => 100));
    protected $file_field='picture_url';
    protected $allowed_types = 'gif|jpg|jpeg|png';

    public function update_photo($id, $redirect = true)
    {
        return $this->update_file($id, $redirect = true);
    }

    public function delete_photo($id)
    {
        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($id);

        switch ($this->file_server_type) {
            case 'AWS':
                $this->_delete_S3($content['picture_url']);
                break;
            case 'AzureRM':
                $this->_delete_azure_blob($content['picture_url']);
                break;
            case 'FTP':
                $this->_delete_ftp($content['picture_url']);
                break;
            default:
                $this->_delete_file($content['picture_url']);
        }

        if ($this->{$this->model}->delete_photo($id)) {
            $this->session->set_flashdata('message', array('type' => 'success', 'message' => _('Successfully Delete Photo')));
        } else {
            $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('Fail To Delete Photo')));
        }

        redirect($this->router->fetch_class() . '?id=' . $id);
    }

    protected function get_files($filename)
    {
        $path = lcfirst($this->directory) . '/' . $this->category_directory . '/';
        $files = array(
            $path . $filename,
            $path . 'large_thumb_' . $filename,
            $path . 'medium_thumb_' . $filename,
            $path . 'small_thumb_' . $filename,
        );

        return $files;
    }

    protected function file_upload()
    {
        if ($this->input->post('data_image')) {
            $upload_datas = $this->_upload_data_image($this->input->post('data_image'));
        } else {
            $upload_datas = $this->do_upload();
        }

        $photos = array();
        if (empty($upload_datas)) {
            print_r($this->upload->display_errors());
            exit;
        } else {
            foreach ($upload_datas as $upload_data) {
                $photos[] = $this->_makeThumbnail($upload_data);
            }

            switch ($this->file_server_type) {
                case 'ftp':
                    $upload_data['full_url'] = $this->_upToFTP($photos);
                    break;
                case 'AWS':
                    $upload_data['full_url'] = $this->_upToS3($photos);
                    break;
                case 'AzureRM':
                    $upload_data['full_url'] = $this->_upToAzureBlob($photos);
                    break;
            }
        }

        return $upload_datas;
    }
    

    protected function _upload_data_image($data_image)
    {
        //$uploads_directory = $this -> config -> item('uploads_directory');

        $uploads_directory = 'files';

        $directory = lcfirst($this->directory);

        if (!file_exists($uploads_directory)) {
            throw new Exception('upload directory not exists1', 1);
        }

        if ($this->file_server_type == 'local') {
            $directory_array = array($directory, $this->category_directory);
        } else {
            $directory_array = array('tmp', $directory, $this->category_directory);
        }

        $path = $this->check_and_make_directory($uploads_directory, $directory_array);
        $this->upload_path = $path;

        $photoSource = str_replace('data:image/png;base64,', '', $data_image);
        $photoSource = str_replace(' ', '+', $photoSource);
        $photoSource = base64_decode($photoSource);
        $file_path = $path . DIRECTORY_SEPARATOR . uniqid() . '.png';
        file_put_contents($file_path, $photoSource);
        $image_info = getimagesize($file_path);

        $photo_info = array(array(
            'file_name' => basename($file_path),
            'file_type' => $image_info['mime'],
            'file_path' => realpath(dirname($file_path)) . DIRECTORY_SEPARATOR,
            'full_path' => realpath($file_path),
            'file_ext' => 'png',
            'file_size' => filesize($file_path),
            'image_width' => $image_info[0],
            'image_height' => $image_info[1],
            'image_size_str' => $image_info[3],
        ));

        return $photo_info;
    }

    protected function _makeThumbnail(array $file)
    {
        $config['image_library'] = 'gd2';
        $config['create_thumb'] = true;
        $config['maintain_ratio'] = false;
        $config['thumb_marker'] = '';
        $config['source_image'] = $file['full_path'];
        $this->load->library('image_lib', $config);

        foreach ($this->thumb_array as $key => $value) {
            $config['new_image'] = $key . '_' . $file['file_name'];
            $config['width'] = $value['width'];
            $config['height'] = $value['height'];
            $this->image_lib->initialize($config);
            if ($this->image_lib->resize()) {
                $data[$key] = array('file_name' => $config['new_image'], 'file_path' => $file['file_path'], 'full_path' => $file['file_path'] . $config['new_image']);
            }
        }

        $data['origin'] = array('file_name' => $file['file_name'], 'file_path' => $file['file_path'], 'full_path' => $file['file_path'] . $file['file_name']);

        return $data;
    }

    public function delete($id)
    {
        $this->load->library('form_validation');
        $this->set_delete_form_validation();
        $this->set_message();

        $content = $this->get_view_content_data($id);

        if ($this->form_validation->run() == false) {
            $this->return_data['data']['content'] = $content;
            if ($this->format == 'html') {
                $this->delete_confirm($id);
            } else {
                echo json_encode(array('result' => 'error', 'message' => validation_errors()));
            }
        } else {
            if ($this->{$this->model}->delete($id)) {
                if (!empty($content['picture_url'])) {
                    switch ($this->file_server_type) {
                    case 'AWS':
                        $this->_delete_S3($content['picture_url']);
                        break;
                    case 'AzureRM':
                        $this->_delete_azure_blob($content['picture_url']);
                        break;
                    case 'FTP':
                        $this->_delete_ftp($content['picture_url']);
                        break;
                    default:
                        $this->_delete_file($content['picture_url']);
                    }
                }

                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'success', 'message' => $this->delete_complete_message($content), 'redirect_path' => $this->delete_redirect_path($content)));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'success', 'message' => $this->delete_complete_message($content)));
                    redirect($this->delete_redirect_path($content));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(array('result' => 'error', 'message' => _('Delete Fail')));
                } else {
                    $this->session->set_flashdata('message', array('type' => 'danger', 'message' => '삭제되지 못했습니다'));
                    redirect($this->delete_redirect_path($content));
                }
            }
        }
    }
}
