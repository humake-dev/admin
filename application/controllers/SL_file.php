<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class SL_file extends SL_Controller
{
    protected $upload_path = false;
    protected $directory;
    protected $category_directory;
    protected $upload_filename = 'file';
    protected $file_model;
    protected $file_field='file_url';
    protected $file_id_field;

    public function __construct()
    {
        parent::__construct();

        if ($this->input->is_cli_request()) {
            return true;
        }

        if (empty($this->directory)) {
            $this->directory = camelize(lcfirst($this->model));
        }

        if (empty($this->category_directory)) {
            $this->category_directory = $this->session->userdata('branch_id');
        }

        if(empty($this->file_model)) {
            $this->file_model=$this->model;
        }

        if(empty($this->file_id_field)) {
            $this->file_id_field=decamelize($this->model).'_id';
        }
    }

    public function delete_photo($id)
    {
        $this->load->model($this->model);
        $content = $this->{$this->model}->get_content($id);

        switch ($this->file_server_type) {
            case 'AWS':
                $this->_delete_S3($content[$this->file_field]);
                break;
            case 'AzureRM':
                $this->_delete_azure_blob($content[$this->file_field]);
                break;
            case 'FTP':
                $this->_delete_ftp($content[$this->file_field]);
                break;
            default:
                $this->_delete_file($content[$this->file_field]);
        }

        if ($this->{$this->model}->delete_photo($id)) {
            $this->session->set_flashdata('message', array('type' => 'success', 'message' => _('Successfully Delete Photo')));
        } else {
            $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('Fail To Delete Photo')));
        }

        redirect($this->router->fetch_class() . '?id=' . $id);
    }

    protected function _delete_ftp($filename)
    {
    }

    protected function _delete_file($filename)
    {
        $files = $this->get_files($filename);

        foreach ($files as $file) {
            //echo FCPATH.'files'.DIRECTORY_SEPARATOR.$file.'<br />';
            unlink(FCPATH . 'files' . DIRECTORY_SEPARATOR . $file);
        }
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

    protected function _delete_azure_blob($filename)
    {
        $files = $this->get_files($filename);

        try {
            $blobRestProxy = \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobService('DefaultEndpointsProtocol=https;AccountName=' . $_ENV['AZURE_STORAGE_ACCOUNT_NAME'] . ';AccountKey=' . $_ENV['AZURE_STORAGE_ACCESS_KEY']);

            foreach ($files as $file) {
                $blobRestProxy->deleteBlob($_ENV['FOG_DIRECTORY'], $file);
            }

            return true;
        } catch (\MicrosoftAzure\Storage\Common\Exceptions\ServiceException $e) {
            error_log($e->getMessage());

            return false;
        }
    }

    protected function _delete_S3($filename)
    {
        $files = $this->get_files($filename);

        try {
            $s3 = new Aws\S3\S3Client($this->s3_options);

            foreach ($files as $file) {
                $result = $s3->deleteObject(['Bucket' => $_ENV['FOG_DIRECTORY'], 'Key' => $file]);
            }

            return true;
        } catch (Aws\Exception\AwsException $e) {
            error_log($e->getMessage());

            return false;
        }
    }

    protected function check_and_make_directory($uploads_directory, array $directory_array)
    {
        if (!count($directory_array)) {
            throw new Exception('upload directory not exists', 1);
        }

        $check_directory = $uploads_directory;
        foreach ($directory_array as $value) {
            $check_directory .= DIRECTORY_SEPARATOR . $value;

            if (!file_exists($check_directory)) {
                if (!mkdir($check_directory)) {
                    throw new Exception($check_directory . ' can not make', 1);
                }
            }
        }

        return $check_directory;
    }

    protected function ftp_check_and_make_directory($conn_id, $uploads_directory, array $directory_array)
    {
        if (!count($directory_array)) {
            throw new Exception('upload directory not exists', 1);
        }

        $check_directory = $uploads_directory;
        if (!ftp_chdir($conn_id, $check_directory)) {
            if (!ftp_mkdir($conn_id, $check_directory)) {
                throw new Exception($check_directory . ' can not make', 1);
            }
        }

        foreach ($directory_array as $value) {
            if (!ftp_chdir($conn_id, $value)) {
                if (!ftp_mkdir($conn_id, $value)) {
                    throw new Exception($value . ' can not make', 1);
                } else {
                    if (!ftp_chdir($conn_id, $value)) {
                        throw new Exception($check_directory . ' can not move', 1);
                    }
                }
            }
        }

        return $uploads_directory . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $directory_array);
    }

    protected function _upToAzureBlob(array $files)
    {
        $blobRestProxy = \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobService('DefaultEndpointsProtocol=https;AccountName=' . $_ENV['AZURE_STORAGE_ACCOUNT_NAME'] . ';AccountKey=' . $_ENV['AZURE_STORAGE_ACCESS_KEY']);
        // $options = new \MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions();
        // $options->setPublicAccess(\MicrosoftAzure\Storage\Blob\Models\PublicAccessType::CONTAINER_AND_BLOBS);
        try {
            foreach ($files as $file) {
                foreach ($file as $index => $value) {
                    //upload
                    $blob_name = lcfirst($this->directory) . '/' . $this->category_directory . '/' . $value['file_name'];
                    $content = fopen($value['full_path'], 'r');
                    //  $options->setBlobContentType("image/gif");
                    $blobRestProxy->createBlockBlob($_ENV['FOG_DIRECTORY'], $blob_name, $content);
                }
            }

            //Upload blob
        } catch (\MicrosoftAzure\Storage\Common\Exceptions\ServiceException $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code . ': ' . $error_message . '<br />';
        }
    }

    protected function _upToS3(array $files)
    {
        try {
            $s3 = new Aws\S3\S3Client($this->s3_options);

            foreach ($files as $file) {
                foreach ($file as $index => $value) {
                    $result = $s3->putObject([
                        'Bucket' => $_ENV['FOG_DIRECTORY'],
                        'Key' => lcfirst($this->directory) . '/' . $this->category_directory . '/' . $value['file_name'],
                        'SourceFile' => $value['full_path'],
                        'ACL' => 'public-read',
                    ]);
                }
            }
        } catch (Aws\Exception\AwsException $e) {
            //error_log($e->getMessage());
            print_r($e->getMessage());
            exit;
        }

        return true;
    }

    protected function _upToFTP($id, array $files)
    {
        $this->config->load('ftp');

        $ftp_server = $this->config->item('ftp_server');
        $ftp_user_name = $this->config->item('ftp_user');
        $ftp_user_pass = $this->config->item('ftp_password');
        $base_uploads_directory = $this->config->item('ftp_uploads_directory');

        $conn_id = ftp_connect($ftp_server);
        ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

        $directory = $this->directory;
        $directory_array = array($directory, 'photo', $id);

        $upload_directory = $this->ftp_check_and_make_directory($conn_id, $base_uploads_directory, $directory_array);

        foreach ($files as $index => $value) {
            $fp = fopen($value['full_path'], 'r');

            if (ftp_fput($conn_id, $upload_directory . DIRECTORY_SEPARATOR . $value['file_name'], $fp, FTP_BINARY)) {
                //echo 'Successfully uploaded '.$value['file_name']."\n";
            } else {
                //echo 'There was a problem while uploading '.$value['file_name']."\n";
            }
        }

        ftp_close($conn_id);
        fclose($fp);

        return str_replace($base_uploads_directory, 'http://' . $ftp_server, $upload_directory . DIRECTORY_SEPARATOR . $value['file_name']);
    }

    protected function after_insert_data($id, $data)
    {
        $this->file_insert($id);
    }

    protected function after_update_data($id, $data)
    {
        $this->file_insert($id);
    }


    public function update_file($id, $redirect = true)
    {
        $this->load->model($this->model);

        try {
            $upload_datas = $this->file_upload($id);

            foreach ($upload_datas as $upload_data) {
                $upload_file_data = array();
                $upload_file_data[$this->file_field] = $upload_data['file_name'];
                $upload_file_data['id'] = $id;

                if (!$this->{$this->model}->update($upload_file_data)) {
                    throw new Exception('Error Processing Request', 1);
                }
            }

            if ($this->format == 'json') {
                echo json_encode(array('result' => 'success', 'photo' => urlencode(getPhotoPath(lcfirst($this->directory), $this->category_directory, $upload_data['file_name'], 'large'))));
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
                echo json_encode(array('result' => 'error', 'message' => $e->getMessage()));
            } else {
                echo $error;
            }

            return false;
        }
    }

    protected function file_upload()
    {
        $files = array();

        $upload_datas = $this->do_upload();

        if (empty($upload_datas)) {
            print_r($this->upload->display_errors());
            exit;
        } else {
            foreach ($upload_datas as $upload_data) {
                $files[]['origin'] = array('file_name' => $upload_data['file_name'], 'file_path' => $upload_data['file_path'], 'full_path' => $upload_data['file_path'] . $upload_data['file_name']);
            }

            switch ($this->file_server_type) {
                case 'ftp':
                    $upload_data['full_url'] = $this->_upToFTP($files);
                    break;
                case 'AWS':
                    $upload_data['full_url'] = $this->_upToS3($files);
                    break;
                case 'AzureRM':
                    $upload_data['full_url'] = $this->_upToAzureBlob($files);
                    break;
            }
        }

        return $upload_datas;
    }

    protected function file_insert($id)
    {
        $upload_do = false;
        foreach ($_FILES[$this->upload_filename]['error'] as $index => $error) {
            if ($error) {
                unset($_FILES[$this->upload_filename]['name'][$index]);
                unset($_FILES[$this->upload_filename]['type'][$index]);
                unset($_FILES[$this->upload_filename]['tmp_name'][$index]);
                unset($_FILES[$this->upload_filename]['error'][$index]);
                unset($_FILES[$this->upload_filename]['size'][$index]);
                $upload_do = true;
            }
        }

        if (!count($_FILES[$this->upload_filename]['error'])) {
            return false;
        }

        $this->load->model($this->file_model);

        try {
            $upload_datas = $this->file_upload($id);
            
            foreach ($upload_datas as $upload_data) {
                $upload_file_data = array();
                $upload_file_data[$this->file_field] = $upload_data['file_name'];
                $upload_file_data[$this->file_id_field] = $id;

                if (!$this->{$this->file_model}->insert($upload_file_data)) {
                    throw new Exception('Error Processing Request', 1);
                }
            }

            if ($this->format == 'json') {
                echo json_encode(array('result' => 'success', 'photo' => getPhotoPath(lcfirst($this->model), $this->session->userdata('branch_id'), $upload_data['file_name'], 'large')));
            } else {
                return true;
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

    protected function do_upload($filename='file')
    {
        $uploads_directory = 'files';
        $directory = lcfirst($this->directory);

        if (!file_exists($uploads_directory)) {
            throw new Exception('upload directory not exists1', 1);
        }

        $directory_array = array($directory, $this->category_directory);
        $this->upload_path = $this->check_and_make_directory($uploads_directory, $directory_array);

        if (!$this->upload_path) {
            throw new Exception('upload directory not exists2', 1);
        }

        $config['upload_path'] = $this->upload_path;
        $config['allowed_types'] = '*';

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_multi_upload($this->upload_filename)) {
            $error = array('error' => $this->upload->display_errors());
            $this->session->set_flashdata('error', $error['error']);

            print_r($error);

            return false;
        }

        return $this->upload->get_multi_upload_data();
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
                if(!empty($content[$this->file_field])) {
                    switch ($this->file_server_type) {
                        case 'AWS':
                            $this->_delete_S3($content[$this->file_field]);
                            break;
                        case 'AzureRM':
                            $this->_delete_azure_blob($content[$this->file_field]);
                            break;
                        case 'FTP':
                            $this->_delete_ftp($content[$this->file_field]);
                            break;
                        default:
                            $this->_delete_file($content[$this->file_field]);
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
