<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Import_enrolls extends SL_Controller
{
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

    protected function load_check($file)
    {
        $excelReader = PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
        $excelObj = $excelReader->load($file);
        $worksheet = $excelObj->getSheet(0);
        $lastRow = $worksheet->getHighestRow();

        $enrolls = array();

        for ($row = 5; $row <= $lastRow; ++$row) {
            $enroll = array();

            $member_id = $worksheet->getCell('F' . $row)->getValue();

            if(empty($member_id)) {
                continue;
            }
            $enroll['member_id']=$member_id;               

            $start_end_date = $worksheet->getCell('G' . $row)->getValue();
            // $user['transaction_date']=$start_end_date;
            $start_end_date = preg_replace('/\s+/u', ' ', $start_end_date);
            $start_end_date = trim($start_end_date);

            $a_start_end_date=explode('~', $start_end_date);

            $start_date = trim($a_start_end_date[0]);
            $end_date = trim($a_start_end_date[1]);

            $enroll['start_date']=$start_date;
            $enroll['end_date']=$end_date;

            $enroll['have_datetime']=$start_date.' 00:00:00';            

            $transaction_date = $worksheet->getCell('I' . $row)->getValue();
            $enroll['transaction_date']=$transaction_date;
            $enroll['created_at']=$transaction_date;
            $enroll['updated_at']=$transaction_date;

            $quantity = $worksheet->getCell('B' . $row)->getValue();
            $enroll['quantity']=$quantity;

            $payment = $worksheet->getCell('J' . $row)->getValue(); 
            $enroll['payment']= $payment;
            

            $memo = $worksheet->getCell('K' . $row)->getValue();

            $phone = $worksheet->getCell('E' . $row)->getValue();
            $enroll['phone']=$phone;
         

            if(!empty($memo)) {
// 줄바꿈, 탭, 유니코드 공백 제거
$memo = preg_replace('/\s+/u', ' ', $memo);
$memo = trim($memo);

                $enroll['memo']=$memo; 
            }
            $memo='';

            $enroll['use_quantity']=0;

            $enrolls[]=$enroll;
        }
    
        $this->load->model('Import');
        $this->Import->insert_enroll($enrolls);        
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

