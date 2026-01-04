<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Import_rents extends SL_Controller
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

        $rents = array();

        for ($row = 5; $row <= $lastRow; ++$row) {
            $rent = array();

            $member_id = $worksheet->getCell('F' . $row)->getValue();

            if(empty($member_id)) {
                continue;
            }

            $rent['member_id']=$member_id;

            $phone = $worksheet->getCell('D' . $row)->getValue();
            $rent['phone']=$phone;

            $start_date = $worksheet->getCell('J' . $row)->getValue();

            if(empty($start_date)) {
                continue;
            } else {
                $start_date = preg_replace('/\s+/u', ' ', $start_date);
                $start_date = trim($start_date);
                $rent['start_date']=$start_date;                
            }

            $end_date = $worksheet->getCell('K' . $row)->getValue();            

            if(!empty($end_date)) {            
                $end_date = preg_replace('/\s+/u', ' ', $end_date);
                $end_date = trim($end_date);
                $rent['end_date']=$end_date;                
            }
            
            $rent['transaction_date']=$start_date;
            $rent['created_at']=$start_date;
            $rent['updated_at']=$start_date;
            
            $start_obj = new DateTime($start_date);
            $end_obj   = new DateTime($end_date);
            $end_obj->modify('+1 day');

            $diff = $start_obj->diff($end_obj);

            // 년도 * 12 + 개월
            $rent['insert_quantity'] = ($diff->y * 12) + $diff->m;            

            $quantity = $worksheet->getCell('M' . $row)->getValue();
            $rent['no']=$quantity;

            $rents[]=$rent;
        }
    
        $this->load->model('Import');
        $this->Import->insert_rent($rents);        
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

