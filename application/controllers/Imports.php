<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Validate_person.php';

class Imports extends SL_Controller
{
    use Validate_person;

    protected $check_permission = false;    
    protected $directory='importExcel';

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

    protected function isValidDate2($dateString)
    {
    $timestamp = strtotime($dateString);
    if ($timestamp === false) return false;

    $year  = date('Y', $timestamp);
    $month = date('m', $timestamp);
    $day   = date('d', $timestamp);

    return checkdate($month, $day, $year);
    }

    protected function load_check($file)
    {
        $excelReader = PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
        $excelObj = $excelReader->load($file);
        $worksheet = $excelObj->getSheet(0);
        $lastRow = $worksheet->getHighestRow();

        $users = array();

        for ($row = 2; $row <= $lastRow; ++$row) {
            $user = array();

            $name = $worksheet->getCell('C' . $row)->getValue();
            
            $user['name']=$name;

            $phone = $worksheet->getCell('D' . $row)->getValue();
            $user['phone']=$phone;
            $user['card_no'] = $this->create_card_no($phone);            
            
            $c_gender = $worksheet->getCell('E' . $row)->getValue();
            if($c_gender=='남') {
                $gender=1;
            } else {
                $gender=0;
            }
            $user['gender']=$gender;
            
            $birthday = $worksheet->getCell('Y' . $row)->getFormattedValue();
            if(!empty($birthday)) {
                if ($this->isValidDate2($birthday)) {                
                    $user['birthday']=$birthday;
                } else {
                    $user['birthday']=null;                    
                }
            }

            $registration_date = $worksheet->getCell('AE' . $row)->getValue();
            $user['registration_date']=$registration_date;              
            
            $memo = $worksheet->getCell('AG' . $row)->getValue();



            if(!empty($memo)) {
// 줄바꿈, 탭, 유니코드 공백 제거
$memo = preg_replace('/\s+/u', ' ', $memo);
$memo = trim($memo);

                $user['memo']=$memo; 
            }

            $users[]=$user;
        }

        $this->load->model('Import');
        $this->Import->insert_user($users);
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
            redirect('/imports/add', 'refresh');
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

