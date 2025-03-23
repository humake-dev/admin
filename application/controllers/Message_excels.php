<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Validate_person.php';

class Message_excels extends SL_Controller
{
    use Validate_person;

    protected $permission_controller = 'messages';
    protected $directory='messageExcel';

    public function __construct()
    {
        parent::__construct();

        if (empty($this->category_directory)) {
            $this->category_directory = $this->session->userdata('branch_id');
        }
    }    

    protected function index_data($category_id = null) {
        
    }

    public function add()
    {
        $this->load->library('form_validation');
        $this->set_form_validation();
        $this->set_message();

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
                $this->set_add_form_data();
                $this->render_format();
            } else {
                echo json_encode(['result' => 'error', 'message' => validation_errors()]);
            }
        } else {
            ini_set('memory_limit', '256M');
            ini_set('max_execution_time', '300');

            $file = $this->do_upload('file');
            $params=$this->load_check($file['full_path']);

            if($params) {
                if ($this->format == 'json') {
                    echo json_encode(['result' => 'success']);
                } else {
                    //$this->session->set_flashdata('message', ['type' => 'success', 'message' => $this->insert_complete_message($id)]);
                    redirect('/messages/add?message_type=sms&'.$params);
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(['result' => 'error', 'message' => _('Please, Insert Less Than or Equal To 200')]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'danger', 'message' => _('Please, Insert Less Than or Equal To 200')]);
                    redirect('/message-excels/add');
                }
            }
        }
    }

    protected function load_check($file)
    {
        $excelReader = PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
        $excelObj = $excelReader->load($file);
        $worksheet = $excelObj->getSheet(0);
        $lastRow = $worksheet->getHighestRow();

        $this->load->model('User');
        $this->load->model('TempUser');

        $users = array();
        $temp_users = array();

        for ($row = 2; $row <= $lastRow; ++$row) {
            $cell_a = trim($worksheet->getCell('A' . $row)->getValue());

            if(empty($cell_a)) {
                continue;
            }

            if($user=$this->User->get_content_by_phone($this->create_valid_phone($cell_a))) {
                $users[]=$user['id'];
            } else {
                if($temp_user=$this->TempUser->get_content_by_phone($this->create_valid_phone($cell_a))) {
                    $temp_users[]=$temp_user['id'];
                } else {
                    $name = trim($worksheet->getCell('B' . $row)->getValue());

                    if(empty(trim($name))) {
                        $name='메세지보내기생성 사용자';
                    }

                    $temp_users[]=$this->TempUser->insert(array('name'=>$name,'phone'=>$this->create_valid_phone($cell_a)));
                }
            }            
        }

        if((count($users)+count($temp_users))>=200) {
            return false;
        }

        return http_build_query(array('user'=>$users,'temp_user'=>$temp_users));
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation -> set_rules('file', _('Excel'), 'callback_file_uploaded');
    }

    public function file_uploaded() {

        $this->form_validation->set_message('file', 'Please select file.');
        if (!empty($_FILES['file'])) {
            if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
                return true;
            }
        }
        return false;        
    }    

    public function do_upload($filename)
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

        if (!$this->upload->do_upload($filename)) {
            $error = array('error' => $this->upload->display_errors());
            $this->session->set_flashdata('error', $error['error']);

            print_r($error);
            exit;
            redirect('/message-excels/add', 'refresh');
        }

        return $this->upload->data();
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
}
