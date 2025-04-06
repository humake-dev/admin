<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Push.php';

class Reservations extends SL_Controller
{
    use Push;

    protected $model = 'Reservation';
    protected $use_calendar = true;
    protected $script = 'reservations/index.js';
    protected $mode = 'default';
    protected $type = 'day';

    protected function reservation_grant($reservation_user_id)
    {
        $this->load->model('ReservationUser');
        $content = $this->ReservationUser->get_content($reservation_user_id);

        if (!$content) {
            throw new Exception('Error');
        }

        $result = $this->ReservationUser->complete_grant($content['id']);

        if (!$result) {
            throw new Exception('Error');
        }

        $this->load->model('Enroll');
        $enroll = $this->Enroll->get_content($content['enroll_id']);
        $this->Enroll->use_quantity($enroll);

        $account_id = null;
        if (empty($enroll['trainer_id'])) {  // 담당강사가 없고
            $manager_id = $content['manager_id'];
        } else { //  있고
            if ($content['manager_id'] == $enroll['trainer_id']) {
                $manager_id = $enroll['trainer_id'];

                if (isset($enroll['commission'])) { // 수수료 설정이 되어있으면
                    $commission = $enroll['commission'];
                }
            } else {
                $manager_id = $content['manager_id'];
            }
        }

        $this->load->model('Employee');
        $employee = $this->Employee->get_content($manager_id);

        if (empty($commission)) { // 수수료 설정이 안되어 있으면
            //if (empty($employee['commission_rate'])) {
            //    $employee['commission_rate']=36;
            //} 

            $commission = ($enroll['price'] / $enroll['insert_quantity']) * ($employee['commission_rate'] / 100); // 강사 수수료가 설정되어 있으면 아래공식으로 수수료 설정
        }

        if (empty($commission)) {
            $account_id = null;
        } else { // 수수료가 설정되었으면
            $account_source = array('account_category_id' => ADD_COMMISSION, 'type' => 'O', 'branch_id' => $this->session->userdata('branch_id'));
            $account_source['order_id'] = $enroll['order_id'];
            $account_source['course_id'] = $enroll['course_id'];
            $account_source['enroll_id'] = $enroll['id'];
            $account_source['user_id'] = $enroll['user_id'];
            $account_source['employee_id'] = $manager_id;
            $account_source['cash'] = $commission;

            $this->load->model('Account');
            $account_id = $this->Account->insert($account_source);
        }

        $this->load->model('EnrollUseLog');
        $this->EnrollUseLog->insert(array('enroll_id' => $enroll['id'], 'type' => "confirm", 'account_id' => $account_id, 'reservation_user_id' => $content['id']));

        return $result;
    }

    protected function set_search_data()
    {
        $this->load->model($this->model);

        switch ($this->input->get('type')) {
            case 'month':
                $this->type = $this->input->get('type');
                $date_obj = new DateTime($this->date, $this->timezone);
                $date_obj->modify('first day of this month');
                $start_time = $date_obj->format('Y-m-d H:i:s');
                $this->date = $date_obj->format('Y-m-d');

                $date_obj->modify('last day of this month');
                $eDate = $date_obj->format('Y-m-d H:i:s');

                $date_time_obj = new DateTime($this->date, $this->timezone);
                $date_time_obj->modify('first day of previous month');
                $prevDate = $date_time_obj->format('Y-m-d');

                $date_time_obj = new DateTime($this->date, $this->timezone);
                $date_time_obj->modify('first day of next month');
                $nextDate = $date_time_obj->format('Y-m-d');
                break;
            case 'week':
                $this->type = $this->input->get('type');
                $date_time_obj = new DateTime($this->date, $this->timezone);
                $date_time_obj->modify('previous monday');
                $prevDate = $date_time_obj->format('Y-m-d');

                $date_time_obj = new DateTime($this->date, $this->timezone);
                $date_time_obj->modify('next monday');
                $nextDate = $date_time_obj->format('Y-m-d');

                $date_time_obj = new DateTime($this->date, $this->timezone);
                $date_time_obj->modify('+6 day');
                $eDate = $date_time_obj->format('Y-m-d');
                break;
            default:
                $date_time_obj = new DateTime($this->date, $this->timezone);
                $date_time_obj->modify('-1 day');
                $prevDate = $date_time_obj->format('Y-m-d');

                $date_time_obj = new DateTime($this->date, $this->timezone);
                $date_time_obj->modify('+1 day');
                $nextDate = $date_time_obj->format('Y-m-d');

                $eDate = $nextDate;
        }

        $time = null;
        if ($this->input->get_post('time')) {
            $time = $this->input->get_post('time');

            $datetime_obj = new DateTime($this->date . ' ' . $time . ':00', $this->timezone);
            $start_time = $datetime_obj->format('Y-m-d H:i:s');
            $datetime_obj->add(new DateInterval('PT1H'));
            $datetime_obj->modify('-1 Minute');
            $end_time = $datetime_obj->format('Y-m-d H:i:s');
        } else {
            $start_time = null;
        }

        if ($this->input->get('date')) {
            $this->mode = 'list';
        }

        $this->{$this->model}->type = $this->type;
        $this->{$this->model}->date = $this->date;
        $this->{$this->model}->e_date = $eDate;
        $this->{$this->model}->start_time = $start_time;

        if (isset($end_time)) {
            $this->{$this->model}->end_time = $end_time;
            if (isset($this->ReservationBlock)) {
                $this->ReservationBlock->end_time = $end_time;
            }
        }

        $this->return_data['search_data']['type'] = $this->type;
        $this->return_data['search_data']['mode'] = $this->mode;
        $this->return_data['search_data']['prev_date'] = $prevDate;
        $this->return_data['search_data']['next_date'] = $nextDate;
        $this->return_data['search_data']['e_date'] = $eDate;
        $this->return_data['search_data']['time'] = $time;
        $this->return_data['search_data']['start_time'] = $start_time;
    }

    protected function index_data($category_id = null)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->form_validation->set_rules('type', _('Type'), 'in_list[day,week,month]');
        $this->form_validation->set_rules('trainer', _('Trainer'), 'integer');
        $this->form_validation->set_rules('user', _('User'), 'integer');
        $this->set_message();
        $this->set_page();
        $this->set_search_data();

        if ($this->form_validation->run() == false) {
            if ($this->Acl->has_permission('employees') or $this->session->userdata('is_fc') or $this->session->userdata('role_id')<=5) {
                $this->return_data['data']['admin'] = $this->get_admin_list('trainer');
            } else {
                $this->{$this->model}->manager_id = $this->session->userdata('admin_id');
                $manager_id = $this->session->userdata('admin_id');
            }
        } else {
            if ($this->Acl->has_permission('employees') or $this->session->userdata('is_fc') or $this->session->userdata('role_id')<=5) {
                if ($this->input->get('trainer')) {
                    $this->{$this->model}->manager_id = $this->input->get('trainer');
                    $manager_id = $this->input->get('trainer');
                }
            } else {
                $this->{$this->model}->manager_id = $this->session->userdata('admin_id');
                $manager_id = $this->session->userdata('admin_id');
            }

            if ($this->mode == 'list') {
                if (isset($manager_id)) {
                    $this->{$this->model}->manager_id = $manager_id;
                }

                $list = $this->{$this->model}->get_index($this->per_page, $this->page);
                $this->return_data['data'] = $list;

                $this->setting_pagination(['total_rows' => $list['total']]);
                $this->return_data['data']['per_page'] = $this->per_page;
                $this->return_data['data']['page'] = $this->page;
            }

            if (isset($manager_id)) {
                $this->return_data['data']['trainer'] = $manager_id;
            }
        }

        $this->return_data['data']['admin'] = $this->get_admin_list('trainer');
        $this->return_data['data']['aside_list'] = $this->{$this->model}->get_aside($this->per_page, $this->page);
        $this->return_data['data']['reservation'] = $this->{$this->model}->get_reservation_index(1000);

        $this->setting_pagination(['total_rows' => $this->return_data['data']['aside_list']['total']]);
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    public function complete($id)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('reservation_user[]', _('Result'), 'required|in_list[complete,no_show,no_show_confirm,delete,grant]');
        $this->set_search_data();
        $this->set_message();

        if ($this->form_validation->run() == false) {
            $this->load->model('ReservationUser');
            $this->ReservationUser->parent_id = $id;
            $this->return_data['data'] = $this->ReservationUser->get_index(1000, 0);

            $this->load->model($this->model);
            $this->return_data['data']['content'] = ['id' => $id];

            if ($this->input->get('trainer')) {
                $this->return_data['data']['trainer'] = $this->input->get('trainer');
            }

            $this->render_format();
        } else {
            if ($this->result_process($this->input->post('reservation_user'))) {
                $this->session->set_flashdata('message', ['type' => 'success', 'message' => _('Successfully Created Article')]);

                $return_param = [];

                if ($this->input->post('type') != 'day') {
                    $return_param['type'] = $this->input->post('type');
                }

                if ($this->input->post('page')) {
                    $return_param['page'] = $this->input->post('page');
                }

                if ($this->input->post('date') != $this->today) {
                    $return_param['date'] = $this->input->post('date');
                }

                if ($this->input->post('trainer')) {
                    $return_param['trainer'] = $this->input->post('trainer');
                }

                if (count($return_param)) {
                    $s_return_param = http_build_query($return_param);
                    redirect('/reservations?' . $s_return_param);
                } else {
                    if(empty($_REQUEST['return_url'])) {
                        redirect('/reservations');
                    } else {
                        redirect($_REQUEST['return_url']);
                    }
                }
            } else {
                redirect('/reservations/complete/' . $id);
            }
        }
    }

    public function select($type='single')
    {
        $this->set_page();

        $course_id=null;
        if ($this->input->get_post('course_id')) {
            $course_id = $this->input->get_post('course_id');
        }

        $trainer_id=null;
        if ($this->session->userdata('role_id') > 5) {
            $trainer_id = $this->session->userdata('admin_id');
        }       

        $this->return_data['data']['user'] =$this->get_select_user($course_id, $trainer_id, $this->per_page,$this->page);

        if ($this->format == 'json') {
            if ($this->return_data['data']['user']['total']) {
                $result = array('result' => 'success');
                $result['total'] = $this->return_data['data']['user']['total'];
                $result['list'] = $this->return_data['data']['user']['list'];

                echo json_encode($result);
            } else {
                echo json_encode(array('result' => 'success', 'total' => $this->return_data['data']['user']['total']));
            }
        } else {
            $this->return_data['data']['type'] = 'single';
            $this->setting_pagination(array('total_rows' => $this->return_data['data']['user']['total']));
            $this->return_data['data']['per_page'] = $this->per_page;
            $this->return_data['data']['page'] = $this->page;       

            if (!empty($course_id)) {
                $this->return_data['data']['course_id'] = $course_id;
            }
    
            if(!empty($trainer_id)) {
                $this->load->model('Employee');
                $this->return_data['data']['trainer']=$this->Employee->get_content($trainer_id);

                $this->return_data['data']['trainer_id'] = $trainer_id;
            }

            $this->script = 'reservations/select.js';
            $this->render_format();
        }
    }

    public function export_excel()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->form_validation->set_rules('type', _('Type'), 'in_list[day,week,month]');
        $this->form_validation->set_rules('trainer', _('Trainer'), 'integer');
        $this->set_message();
        $this->set_search_data();
        $list = $this->{$this->model}->get_aside(100000, 0);

        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator('작성자')
            ->setLastModifiedBy('최종수정자')
            ->setTitle('자격증시험응시리스트')
            ->setSubject('자격증시험응시리스트')
            ->setDescription('자격증시험응시리스트')
            ->setKeywords('자격증 시험')
            ->setCategory('License');

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', _('Course Category'))
            ->setCellValue('B1', _('Course Name'))
            ->setCellValue('C1', _('Manager'))
            ->setCellValue('D1', _('User Name'))
            ->setCellValue('E1', _('Start Date'))
            ->setCellValue('F1', _('Progress Time') . '(' . _('Minute') . ')')
            ->setCellValue('G1', _('Memo'));

        if ($list['total']) {
            foreach ($list['list'] as $index => $value) {
                $memo = '-';
                if (!empty($value['content'])) {
                    $memo = $value['content'];
                }

                $course_name = '-';
                if (!empty($value['course_name'])) {
                    $course_name = $value['course_name'];
                }

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($index + 2), $value['type'])
                    ->setCellValue('B' . ($index + 2), $course_name)
                    ->setCellValue('C' . ($index + 2), $value['manager_name'])
                    ->setCellValue('D' . ($index + 2), $value['users'])
                    ->setCellValue('E' . ($index + 2), $value['start_time'])
                    ->setCellValue('F' . ($index + 2), $value['progress_time'] . _('Minute'))
                    ->setCellValue('G' . ($index + 2), $memo);
            }
        }

        $filename = iconv('UTF-8', 'EUC-KR', '예약목록');

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $objWriter->save('php://output');
    }

    protected function result_process($ids)
    {
        $grantes = [];
        
        foreach ($ids as $key => $value) {
            $grantes[] = $key;
        }

        if (count($grantes)) {
            foreach ($grantes as $grant) {
                $this->reservation_grant($grant);
            }
        }

        return true;
    }

    private function send_message_and_insert_log(array $send_users,array $message)
    {
        $this->load->model('Message');
        $users = $this->Message->get_push_user($send_users);

        if(empty($users)) {
            return false;
        }

        $data = ['type' => 'push', 'user' => $send_users,'title'=>$message['title'],'content'=>$message['content']];

        $this->send_push($users, $message);

        $message_id = $this->Message->insert($data);
        
        $this->load->model('MessageUser');
        foreach ($send_users as $send_user) {
            $this->MessageUser->insert(['message_id' => $message_id, 'user_id' => $send_user,'title'=>$message['title'],'description'=>$message['content']]);
        }

        $this->load->model('MessageSender');
        return $this->MessageSender->insert(['message_id' => $message_id, 'admin_id' => $this->session->userdata('admin_id')]);        
    }

    protected function get_select_user($course_id=null,$trainer_id=null,$per_page=10, $page=0)
    {
        if (!empty($course_id) or !empty($trainer_id)) {
            $user_list = $this->get_enroll_user_list($course_id, $trainer_id,$per_page, $page);
        } else {
            $user_list = $this->get_user_list($per_page,$page);
        }

        return $user_list;
    }

    protected function get_enroll_user_list($course_id, $trainer_id = null, $per_page=10, $page=0)
    {
        $this->load->model('EnrollPtUser');

        if($this->session->userdata('role_id')>2) {
            $this->EnrollPtUser->has_current_primary_only = true;
        }
        $this->EnrollPtUser->course_id = $course_id;
        $this->EnrollPtUser->trainer_id = $trainer_id;


        if ($this->input->get('search_field') and $this->input->get('search_word')) {
            $search_field = $this->input->get('search_field');
            $search_word = trim($this->input->get('search_word'));

            if ($search_field == 'phone') {
                $search_word = str_replace('-', '', $search_word);
            }

            $search_param = array('search_type' => 'field', 'search_field' => $search_field, 'search_word' => $search_word);

            if ($this->input->get('search_field')) {
                $this->return_data['search_data']['search_field'] = $this->input->get('search_field');
            }

            if ($this->input->get('search_word')) {
                $this->return_data['search_data']['search_word'] = $this->input->get('search_word');
            }

            if (isset($search_param)) {
                $this->EnrollPtUser->search_param = $search_param;
            }
        }

        return $this->EnrollPtUser->get_index($per_page, $page);
    }

    protected function set_add_form_data()
    {
        $this->load->model($this->model);

        $this->set_search_data();
        $this->return_data['data']['aside_list'] = $this->{$this->model}->get_aside($this->date, $this->return_data['search_data']['e_date']);

        $this->load->model('Course');
        $this->Course->lesson_type = [4, 5];
        $courses = $this->Course->get_index(100, 0, 'id');

        $default_course_ids = null;

        $this->load->model('Employee');

        if ($this->session->userdata('role_id') > 5) {
            $this->return_data['data']['admin']['content'] = $this->Employee->get_content($this->session->userdata('admin_id'));

            if ($courses['total']) {  
                foreach($courses['list'] as $course) {
                    $default_course_ids[] = $course['id'];
                }
            }
        } else {
            $this->get_admin_list(1000);
            if ($this->input->post('manager')) {
                $this->return_data['data']['admin']['content'] = $this->Employee->get_content($this->input->post('manager'));
            }
        }

        $this->return_data['data']['course'] = $courses;
        $this->script = 'reservations/add.js';
    }

    protected function set_insert_data($data)
    {
        if (!empty($data['course'])) {
            $data['course_id'] = $data['course'];
        }

        if (!empty($data['manager'])) {
            $data['manager_id'] = $data['manager'];
            unset($data['manager']);
        }

        $datetime_obj = new DateTime($data['date'] . ' ' . $data['time'] . ':00', $this->timezone);
        $data['start_time'] = $datetime_obj->format('Y-m-d H:i:s');
        $datetime_obj->add(new DateInterval('PT' . $data['progress_time'] . 'M'));
        $data['end_time'] = $datetime_obj->format('Y-m-d H:i:s');

        unset($data['date']);
        unset($data['time']);

        return $data;
    }

    protected function set_update_data($id, $data)
    {
        $data = $this->set_insert_data($data);
        $data['id'] = $id;

        return $data;
    }

    protected function after_insert_data($id, $data)
    {
        $this->load->model('ReservationUser');
        $this->load->model('Enroll');

        $rm_data = ['reservation_id' => $id];
        
        $rm_data['user_id'] = $data['user_id'];
        
        if ($this->session->userdata('role_id') < 4) {
            $content = $this->Enroll->get_relation_enroll($data['course_id'], $rm_data['user_id'], $data['manager_id']);
        } else {
            $content = $this->Enroll->get_relation_enroll($data['course_id'], $rm_data['user_id']);
        }

        $rm_data['enroll_id'] = $content['id'];
        $reservation_user_id=$this->ReservationUser->insert($rm_data);

        $this->load->model('ReservationUser');
        $reservation_user = $this->ReservationUser->get_content($reservation_user_id);

        $send_users = array();
        $send_users[] = $reservation_user['user_id'];

        $message=array('title' => _('PT Reservation'), 'content' => sprintf(_('PT Reservation, Time is %s'), $this->dt_format($reservation_user['start_time'])));
        $this->send_message_and_insert_log($send_users,$message);
    }

    protected function add_redirect_path($id)
    {
        return $this->router->fetch_class() . '?date=' . $this->input->post('date') . '&time=' . $this->input->post('time');
    }

    protected function after_update_data($id, $data)
    {
        if (!empty($data['course_id'])) {
            $this->load->model('ReservationCourse');
            $this->ReservationCourse->update(['reservation_id' => $id, 'course_id' => $data['course_id']]);
        }
    }

    protected function edit_redirect_path($id)
    {
        return $this->router->fetch_class() . '?date=' . $this->input->post('date') . '&time=' . $this->input->post('time');
    }

    public function check_enroll_quantity($course_id, $user_id)
    {
        if(empty($course_id)) {
            return false;
        }

        if(empty($user_id)) {
            return false;
        }

        $this->load->model('Enroll');
        $this->Enroll->lesson_type = 4;
        $this->Enroll->course_id = $course_id;
        $this->Enroll->user_id = $user_id;
        $this->Enroll->get_current_only = false;
        $this->Enroll->get_not_end_only = true ;

        $enroll = $this->Enroll->get_index(100, 0);

        if (empty($enroll['total'])) {
            $this->load->model('User');
            $user = $this->User->get_content($user_id);

            $this->form_validation->set_message('check_enroll_quantity', sprintf(_('%s is not enough quantity'), $user['name']));
            return false;
        }
        
        $available_qunantity = false;

        $content = $this->Enroll->get_relation_enroll($course_id, $user_id, $this->input->post('manager'));
        
        if (!empty($content)) {
            $available_qunantity = true;
        }
        
        if (empty($available_qunantity)) {
            $this->load->model('User');
            $user = $this->User->get_content($user_id);

            $this->form_validation->set_message('check_enroll_quantity', sprintf(_('%s is not enough quantity'), $user['name']));
            return false;
        }

        return true;
    }

    public function check_period_enroll_available($user_id, $date)
    {
        if(empty($user_id)) {
            return false;
        }

        if(empty($date)) {
            return false;
        }

        if($this->session->userdata('role_id')<3) {
            return true;
        }

        $this->load->model('Enroll');
        $this->Enroll->user_id = $user_id;
        $this->Enroll->get_current_only = false;
        $this->Enroll->lesson_type = 1;

        $enrolls = $this->Enroll->get_index(100, 0, 'end_date', true);

        if (empty($enrolls['total'])) {
            $this->load->model('User');
            $user = $this->User->get_content($user_id);
            
            $this->form_validation->set_message('check_period_enroll_available', sprintf(_('%s is not have period enroll'), $user['name']));
            return false;
        }

        $date_available=false;

        foreach($enrolls['list'] as $enroll) {
            if(new DateTime($enroll['end_date'],$this->timezone)>= new DateTime($date,$this->timezone)) {
                $date_available=true;
            }
        } 

        if (empty($date_available)) {
            $this->load->model('User');
            $user = $this->User->get_content($user_id);

            $this->form_validation->set_message('check_period_enroll_available', sprintf(_('%s is not have period enroll in date'), $user['name']));
            return false;
        }

        return true;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('type', _('Type'), 'required|in_list[PT,FPT,OT,Counsel,Etc]');
        $this->form_validation->set_rules('progress_time', _('Progress Time'), 'required|integer');
        $this->form_validation->set_rules('date', _('Date'), 'required|callback_valid_date');

        if (in_array($this->input->post('type'), ['PT', 'FPT'])) {
            $this->form_validation->set_rules('manager', _('Manager'), 'required|integer');
        } else {
            $this->form_validation->set_rules('manager', _('Manager'), 'integer');
        }

        if ($this->input->post('type') == 'PT') {
            $this->form_validation->set_rules('user_id', _('User'), 'required|integer|callback_check_period_enroll_available['.$this->input->post('date').']');
            $this->form_validation->set_rules('course', _('Course'), 'required|integer|callback_check_enroll_quantity['.$this->input->post('user_id').']');
        } else {
            $this->form_validation->set_rules('user_id', _('User'), 'required|integer');
            $this->form_validation->set_rules('course', _('Course'), 'integer');
        }
    }

    protected function set_delete_data(array $content, $data = null)
    {
        $this->load->model('ReservationUser');
        $this->ReservationUser->parent_id = $content['id'];
        $reservation_users = $this->ReservationUser->get_index(1000, 0);
        
        if (empty($reservation_users['total'])) {
            return false;
        }

        $data['enroll_back'] = ['total' => 0, 'list' => []];
        foreach ($reservation_users['list'] as $reservation_user) {
            if ($reservation_user['complete'] == 3) {
                ++$data['enroll_back']['total'];
                $data['enroll_back']['list'][] = ['id' => $reservation_user['id'], 'enroll_id' => $reservation_user['enroll_id']];
            }
        }

        $this->load->model('ReservationUser');
        $data['reservation_user'] = $this->ReservationUser->get_content($reservation_user['id']);

        return $data;
    }

    protected function after_delete_data(array $content, $data = null)
    {
        if (empty($data['enroll_back']['total'])) {
            if (!empty($data['reservation_user'])) {
                $reservation_user=$data['reservation_user'];

                $send_users =array();
                $send_users[] = $reservation_user['user_id'];
                $message=array('title' => _('PT Reservation Cancel'), 'content' => sprintf(_('PT Reservation Cancel, Time is %s'), $this->dt_format($reservation_user['start_time'])));
                
                return $this->send_message_and_insert_log($send_users, $message);
            }

            return false;
        }

        $this->load->model('Enroll');
        $this->load->model('EnrollUseLog');
        foreach ($data['enroll_back']['list'] as $value) {
            $enroll_content = $this->Enroll->get_content($value['enroll_id']);

            if (!empty($enroll_content)) {
                $this->Enroll->back_quantity($enroll_content);
                $this->EnrollUseLog->delete_by_reservation_user_id($value['id']);
            }
        }

        return true;
    }

    protected function dt_format($datetime)
{
    if (empty($datetime) or $datetime == '0000-00-00') {
        return _('Not Inserted');
    }

    $format = 'Y' . _('Year') . ' n' . _('Month') . ' j' . _('Day'). ' H:i';


    $dateTimeObj = new DateTime($datetime, $this->timezone);

    return $dateTimeObj->format($format);
}

    protected function setting_pagination(array $config)
    {
        $this->load->library('pagination');

        if (empty($config['per_page'])) {
            $config['per_page'] = $this->per_page;
        }

        if ($this->router->fetch_method() == 'edit') {
            $config['base_url'] = base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $config['ep_id'];
        } else {
            $config['base_url'] = base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method();
        }
        $config['page_query_string'] = true;
        $config['use_page_numbers'] = true;
        $config['query_string_segment'] = 'page';

        $query_string = $this->input->get();
        if (isset($query_string['page'])) {
            unset($query_string['page']);
        }

        if (count($query_string) > 0) {
            $config['suffix'] = '&' . http_build_query($query_string, '', '&');
            $config['first_url'] = $config['base_url'] . '?' . http_build_query($query_string, '', '&');
        }

        $config['num_links'] = 2;
        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = _('First');
        $config['first_tag_open'] = '<li class="prev page-item">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = _('Last');
        $config['last_tag_open'] = '<li class="next page-item">';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = false;
        $config['prev_link'] = false;

        $config['cur_tag_open'] = '<li class="active page-item"><a href="" class="page-link">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $config['attributes'] = ['class' => 'page-link'];
        $this->pagination->initialize($config);
    }
}
