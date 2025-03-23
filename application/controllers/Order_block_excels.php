<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Validate_person.php';

class Order_block_excels extends SL_Controller
{
    use Validate_person;

    protected $script = 'order-block-excels/add.js';
    protected $model = 'OrderBlock';
    protected $permission_controller = 'orders';
    protected $directory='orderBlockExcel';

    public function __construct()
    {
        parent::__construct();

        if (empty($this->category_directory)) {
            $this->category_directory = $this->session->userdata('branch_id');
        }
    }

    protected function index_data($category_id = null) {
        $this->load->library('form_validation');
        $this->set_index_form_validation();
        $this->set_message();

        if ($this->form_validation->run() == false) {
            if ($this->format == 'html') {
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
                    redirect('/order-block-excels/add?user_type=excel&'.$params);
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(['result' => 'error', 'message' => _('Please, Insert Less Than or Equal To 200')]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'danger', 'message' => _('Please, Insert Less Than or Equal To 200')]);
                    redirect('/order-block-excels/index');
                }
            }
        }
    }

    protected function set_add_form_data()
    {
        $this->load->model('productCategory');
        $this->productCategory->type = 'course';
        $this->return_data['search_data']['course_category'] = $this->productCategory->get_index(100, 0);

        $this->load->model('Course');
        $this->Course->status = 1;
        $this->Course->lesson_type=1;
        $this->return_data['search_data']['course'] = $this->Course->get_index(100, 0);

        $this->return_data['data']['user'] = $this->get_order_block_user($this->input->get_post('user'));
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
            $data = $this->set_insert_data($this->input->post(null, true));

            $user_list = $this->get_user($data);

            $this->pdo = $this->load->database('pdo', true);
            $this->load->model('Order');
            $this->load->model('Course');
            $this->load->model('OrderProduct');
            $this->load->model('Enroll');
            $this->load->model('Account');
            $this->load->model('UserContent');

            $enroll_product_id=$data['product_id'];
            $plus_date=$data['period'];
            $transaction_date=$data['transaction_date'];

            $course=$this->Course->get_content_by_product_id($enroll_product_id);
            $course_id=$course['id'];
            $user_count=0;

            foreach($user_list['list'] as $value) {
               $user_id=$value['id'];

                $count_enroll_query=$this->pdo->query('SELECT COUNT(*) AS count FROM orders AS o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE c.lesson_type=1 AND oe.id is NULL AND o.enable=1 AND o.user_id=?',array($user_id));
                $count_enroll=$count_enroll_query->row_array(0);

                if($count_enroll['count']) {
                    $enroll_query=$this->pdo->query('SELECT e.id,e.order_id,e.start_date,e.end_date,o.user_id FROM orders AS o INNER JOIN enrolls as e ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id LEFT JOIN order_ends as oe ON oe.order_id=o.id WHERE c.lesson_type=1 AND o.enable=1 AND oe.id is NULL AND o.user_id=? ORDER BY e.end_date DESC LIMIT 1',array($user_id));
                    $enroll=$enroll_query->row_array(0);


                    $dateObj=new DateTime($enroll['end_date'],$this->timezone);
                    $dateObj->modify('+1 day');
        
                    $start_date=$dateObj->format('Y-m-d');
        
                    $modify_text='+'.($plus_date-1).' days';
        
                    $dateObj->modify($modify_text);
                    $end_date=$dateObj->format('Y-m-d');


                    $order_id = $this->Order->insert(array('user_id'=>$user_id,'transaction_date'=>$transaction_date));
                    $this->OrderProduct->insert(array('order_id' => $order_id, 'product_id' => $enroll_product_id));
                    $this->Enroll->insert(array('user_id'=>$value['id'],'order_id'=>$order_id,'course_id'=>$course_id, 'start_date'=>$start_date, 'end_date'=>$end_date, 'quantity'=>$plus_date, 'insert_quantity'=>$plus_date, 'type'=>'day','have_datetime'=>$this->now));
                    $this->Account->insert(array('account_category_id' => ADD_ENROLL, 'user_id' => $user_id, 'order_id' => $order_id, 'product_id' => $enroll_product_id,'transaction_date'=>$transaction_date));
                }

                
                if($count_enroll['count']) {
                    $user_count++;
                    if (!empty($data['memo'])) {
                        $this->UserContent->insert(array('user_id'=>$value['id'],'content'=>$data['memo']));
                    }
                }
            }


            $data['user_count']=$user_count;

            $this->load->model($this->model);
            if ($id = $this->{$this->model}->insert($data)) {
                $this->after_insert_data($id, $data);

                if ($this->format == 'json') {
                    echo json_encode(['result' => 'success', 'inserted_id' => $id, 'message' => $this->insert_complete_message($id), 'redirect_path' => $this->add_redirect_path($id)]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'success', 'message' => $this->insert_complete_message($id)]);
                    redirect($this->add_redirect_path($id));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(['result' => 'error', 'message' => _('Insert Fail')]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'danger', 'message' => _('Insert Fail')]);
                    redirect($this->router->fetch_class() . '/add');
                }
            }
        }
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data();
        $this->return_data['data']['content'] = $content;
    }

    protected function set_index_form_validation($id = null)
    {
        $this->form_validation -> set_rules('file', _('Excel'), 'callback_file_uploaded');
    }

    public function file_uploaded(){

        $this->form_validation->set_message('file', 'Please select file.');
        if (!empty($_FILES['file'])) {
            if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
                return true;
            }
        }
        return false;        
    }    

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('product_id', _('Product'), 'required|integer');
        $this->form_validation->set_rules('custom_transaction_date', _('Transaction Date'), 'callback_valid_date');
        $this->form_validation->set_rules('memo', _('Memo'), 'trim');
    }

    protected function get_order_block_user($user)
    {
        $list = array('total' => 0);

        if (empty($user)) {
            return $list;
        }

        if (!is_array($user)) {
            return $list;
        }

        $this->load->model('User');
        $this->User->user_id = $user;
        $this->User->all = true;
        return $this->User->get_index(10000, 0);
    }

    protected function get_user($data)
    {
        $this->load->model('User');
        $this->User->user_id=$data['user'];

        return $this->User->get_index(100000, 0);
    }

    protected function load_check($file)
    {
        $excelReader = PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
        $excelObj = $excelReader->load($file);
        $worksheet = $excelObj->getSheet(0);
        $lastRow = $worksheet->getHighestRow();

        $this->load->model('User');

        $users = array();

        for ($row = 2; $row <= $lastRow; ++$row) {
            $cell_b = trim($worksheet->getCell('B' . $row)->getValue());

            if(empty($cell_b)) {
                continue;
            }

            if($user=$this->User->get_content_by_phone($this->create_valid_phone($cell_b))) {
                $users[]=$user['id'];
            }       
        }

        if(count($users)>=200) {
            return false;
        }

        return http_build_query(array('user'=>$users));
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
