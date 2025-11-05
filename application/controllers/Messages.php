<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_photo.php';
require_once 'Search_period.php';
require_once 'SMS_key.php';
require_once 'Push.php';

class Messages extends SL_photo
{
    use Search_period;
    use SMS_key;
    use Push;
    
    protected $model = 'Message';
    protected $script = 'messages/index.js';
    protected $thumb_array = array('medium_thumb' => array('width' => 200, 'height' => 200), 'small_thumb' => array('width' => 100, 'height' => 100));
    protected $use_default_id_key = true;
    protected $message_chunk = 50;
    protected $push_chunk=300;
    protected $file_model='MessagePicture';

    protected function index_data($category_id = null)
    {
        $this->load->model($this->model);

        if($this->input->get('type')) {
            $search_type=$this->input->get('type');

            if(in_array($search_type,array('sms','push'))) {
                $this->{$this->model}->type=$search_type;
            }
        }

        $this->set_page();

        $list = $this->{$this->model}->get_index($this->per_page, $this->page);

        if (!empty($list['total'])) {
            $this->load->model('MessageUser');
            $this->load->model('MessageTempUser');

            foreach ($list['list'] as $index => $value) {
                $this->MessageUser->parent_id = $value['id'];
                $list['list'][$index]['message_users'] = $this->MessageUser->get_index();

                $this->MessageTempUser->parent_id = $value['id'];
                $list['list'][$index]['message_temp_users'] = $this->MessageTempUser->get_index();
            }
        }

        $this->return_data['data'] = $list;

        $this->setting_pagination(array('total_rows' => $list['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;
    }

    protected function get_message_user($user, $push = false)
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

        if ($push == 'push') {
            $this->User->push = true;
        }

        $this->User->all = true;
        return $this->User->get_index(1000, 0);
    }

    protected function get_message_temp_user($temp_user)
    {
        $list = array('total' => 0);

        if (empty($temp_user)) {
            return $list;
        }

        if (!is_array($temp_user)) {
            return $list;
        }

        $this->load->model('TempUser');
        $this->TempUser->id = $temp_user;
        return $this->TempUser->get_index(1000, 0);
    }

    protected function set_add_form_data()
    {
        $this->return_data['data']['user'] = $this->get_message_user($this->input->get_post('user'), $this->input->get_post('type'));
        $this->return_data['data']['temp_user'] = $this->get_message_temp_user($this->input->get_post('temp_user'));

        $this->load->model('EmployeeEnableSendMessage');
        $this->return_data['data']['sender'] = $this->EmployeeEnableSendMessage->get_index(1000, 0);

        $this->layout->add_js('messages/index.js');
        $send_available = true;

        if ($this->input->get_post('type') != 'push') {
            $data_send = $this->get_check_remain();

            if ($data_send->result_code == 1) {
                if (empty($data_send->MMS_CNT)) {
                    $send_available = false;
                }
            } else {
                echo $data_send->message;
            }
        }

        if ($this->input->get('search')) {
            $this->return_data['data']['search_count'] = $this->get_search_user(true, $this->input->get_post('type'));
        }

        if ($this->input->get('counsel_search')) {
            $this->return_data['data']['counsel_search_count'] = $this->get_counsel_user(true);
        }

        $this->return_data['data']['new_key_type']=$this->check_is_sms_key_new();
        $this->return_data['data']['type'] = $this->input->get_post('type');
        $this->return_data['data']['use_default_id_key'] = $this->use_default_id_key;
        $this->return_data['data']['send_available'] = $send_available;
    }

    protected function get_counsel_user($count = false)
    {
        $this->load->model('Counsel');
        $this->Counsel->phone = true;
        $this->Counsel->search = $this->input->get();

        if ($this->input->get('start_date')) {
            $this->Counsel->start_date = $this->input->get('start_date');
        }

        if ($this->input->get('end_date')) {
            $this->Counsel->end_date = $this->input->get('end_date');
        }

        if($this->input->get('search_type')) {
            $this->Counsel->search_type=$this->input->get('search_type');
        }

        if ($count) {
            return $this->Counsel->get_count();
        } else {
            return $this->Counsel->get_index(100000, 0);
        }
    }

    protected function get_search_user($count = false, $type = 'sms')
    {
        if ($this->input->get_post('type') == 'push') {
            $type = 'push';
        }

        $this->load->model('Search');

        $m_search = $this->input->get(null,true);
        $this->Search->search = $m_search;

        if ($this->input->get('show_only_my_user')) {
            if ($this->session->userdata('is_trainer')) {
                $trainer_id = $this->session->userdata('admin_id');
            }

            if ($this->session->userdata('is_fc')) {
                $fc_id = $this->session->userdata('admin_id');
            }
        }

        if ($this->input->get('trainer')) {
            $trainer_id = $this->input->get('trainer');
        }

        if ($this->input->get('fc')) {
            $fc_id = $this->input->get('fc');
        }

        if (!empty($trainer_id)) {
            $this->Search->trainer_id = $trainer_id;
        }

        if (!empty($fc_id)) {
            $this->Search->fc_id = $fc_id;
        }

        if ($type == 'push') {
            $this->Search->device_only = true;
        } else {
            $this->Search->phone_only = true;
        }

        $this->set_search('Search');

        $this->load->model('Course');
        $this->Course->status = 1;
        $courses = $this->Course->get_index(100, 0);

        $this->load->model('Facility');
        $facilities = $this->Facility->get_index(100, 0);

        $this->load->model('ProductRelation');
        $this->ProductRelation->product_relation_type_id=PRIMARY_COURSE_ID;
        $product_relations = $this->ProductRelation->get_index();        

        $is_pt_product = false;
        $all_primary = false;
        $product_id = array();

        if ($this->input->get('product_id')) {
            foreach ($this->input->get('product_id') as $p_id) {
                if (empty($p_id)) {
                    continue;
                }

                if ($courses['total']) {
                    foreach ($courses['list'] as $course) {
                        if ($course['product_id'] == $p_id and $course['lesson_type'] == 4) {
                            $is_pt_product = true;
                        }
                    }
                }

                if (in_array($p_id, ['all_rent', 'all_primary'])) {
                    if ($p_id == 'all_rent') {
                        foreach ($facilities['list'] as $facility) {
                            $product_id[] = $facility['product_id'];
                        }
                    }

                    if ($p_id == 'all_primary') {
                        if(!empty($product_relations['total'])) {
                            $all_primary = true;
                            foreach ($product_relations['list'] as $pr) {
                                $product_id[] = $pr['product_id'];
                            }
                        }
                    }
                } else {
                    $product_id[] = $p_id;
                }
            }
        }

        if (!empty($product_id)) {
            $this->Search->product_id = $product_id;
            $this->Search->all_primary = $all_primary;
        }

        if ($is_pt_product) {
            $this->Search->search_pt = true;
        }        

        $this->set_search($this->model, 30, true);

        $reference_no_display = true;

        if($this->input->get('search_status') == '') {
            if(count($product_id)) {
                $reference_no_display = false;
            }
        } 

        if (!$reference_no_display and $this->input->get('reference_date')) {
            $this->Search->reference_date = $this->input->get('reference_date');
        }

        if($this->input->get('search_type')) {
            $this->Search->search_type=$this->input->get('search_type');
        }

        if ($count) {
            return $this->Search->get_count();
        } else {
            return $this->Search->get_index(100000, 0);
        }
    }

    protected function file_upload()
    {
        $upload_datas = $this->do_upload();

        $photos = array();
        if (empty($upload_datas)) {
            print_r($this->upload->display_errors());
            exit;
        } else {
            foreach ($upload_datas as $upload_data) {
                    $photos[]['origin'] = array('file_name' => $upload_data['file_name'], 'file_path' => $upload_data['file_path'], 'full_path' => $upload_data['file_path'] . $upload_data['file_name']);
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

    protected function set_insert_data($data)
    {
        if (in_array($data['type'], array('push', 'wapos'))) {
            $data['push_key'] = $this->get_push_key();
            $data['sender'] = null;
        }

        if (in_array($data['type'], array('sms', 'wapos'))) {
            $data['sms_key'] = $this->get_sms_key();
            if (empty($data['sender'])) {
                $data['sender'] = $this->return_data['common_data']['branch']['phone'];
            } else {
                $this->load->model('EmployeeEnableSendMessage');
                $sender_content = $this->EmployeeEnableSendMessage->get_content($data['sender']);
                $data['sender'] = $sender_content['phone'];
            }
        }

        if ($this->input->get('search')) {
            $search_users = $this->get_search_user();
            $user = array();

            if ($search_users['total']) {
                foreach ($search_users['list'] as $search_user) {
                    $user[] = $search_user['id'];
                }
                $data['user'] = $user;
            }
        }

        if ($this->input->get('counsel_search')) {
            $search_users = $this->get_counsel_user();
            $user = array();
            $temp_user = array();

            if ($search_users['total']) {
                foreach ($search_users['list'] as $search_user) {
                    if (empty($search_user['user_id'])) {
                        $temp_user[] = $search_user['temp_user_id'];
                    } else {
                        $user[] = $search_user['user_id'];
                    }
                }

                $data['temp_user'] = $temp_user;
                $data['user'] = $user;
            }
        }

        foreach ($_FILES['photo']['error'] as $index => $error) {
            if ($error) {
                unset($_FILES['photo']['name'][$index]);
                unset($_FILES['photo']['type'][$index]);
                unset($_FILES['photo']['tmp_name'][$index]);
                unset($_FILES['photo']['error'][$index]);
                unset($_FILES['photo']['size'][$index]);
            }
        }

        $data['attach_file'] = null;
        if (count($_FILES['photo']['error'])) {
            $data['attach_file'] = $this->file_upload();

            if (count($data['attach_file'])) {
                $data['picture'] = getPhotoPath('message', $this->session->userdata('branch_id'), $data['attach_file'][0]['file_name'], 'medium');
            }
        }

        if (in_array($data['type'], array('push', 'wapos'))) {
            if (empty($data['push_key'])) {
                throw new exception('push_key not exists');
            }
        } else {
            if (empty($data['sms_key'])) {
                throw new exception('sms_key not exists');
            }
        }

        if (isset($data['send_all'])) {
            if ($data['send_all'] == '1') {
                $data['user'] = array();
            }
        }

        return $data;
    }

    protected function message_file_insert($message_id, $upload_datas)
    {
        $this->load->model('MessagePicture');

        try {
            foreach ($upload_datas as $upload_data) {
                $upload_file_data = array();
                $upload_file_data['picture_url'] = $upload_data['file_name'];
                $upload_file_data['message_id'] = $message_id;

                if (!$this->MessagePicture->insert($upload_file_data)) {
                    throw new Exception('Error Processing Request', 1);
                }
            }

            return true;
        } catch (exception $e) {
            $error = $e->getMessage();

            return false;
        }
    }

    protected function send_sms(array $users, array $message_data)
    {
        if (count($users) > 1000) {
            throw new exception('Max Send 1000 User, at a time');
        }

        $client = new GuzzleHttp\Client(['verify' => false]);

        $result = array();
        $receiver = array();
        $destination = array();
        foreach ($users as $value) {
            $receiver[] = $value['phone'];
            $destination[] = $value['phone'] . '|' . $value['name'];
        }

        /* 메세지 전송 API params 만들기 */
        $form_params = array(
            'userid' => $message_data['sms_key']['sms_id'],
            'key' => $message_data['sms_key']['sms_key'],
            'sender' => $message_data['sender'],
            'receiver' => implode(',', $receiver),
            'destination' => implode(',', $destination),
            'title' => $message_data['title'],
            'msg' => $message_data['content'],
            'testmode_yn' => $message_data['testmode_yn'],
        );

        if (empty($message_data['attach_file'])) {
            $send_data = array('form_params' => $form_params);
        } else {
            $multipart = array(array('name' => 'image', 'contents' => fopen($message_data['attach_file'][0]['full_path'], 'r')));
            foreach ($form_params as $key => $value) {
                $multipart[] = array('name' => $key, 'contents' => $value);
            }
            $send_data = array('multipart' => $multipart);
        }

        $response = $client->request('POST', 'https://apis.aligo.in/send/', $send_data);
        $send_result = json_decode($response->getBody());

        $result[] = $send_result;

        return $result;
    }

    public function after_insert_async($id, array $users, array $data, $push= false)
    {
        ini_set('max_execution_time', 600);

        $data['branch_id'] = $this->session->userdata('branch_id');
        $data['admin_id'] = $this->session->userdata('admin_id');

        unset($data['complete_user']);
        unset($data['user']);
        unset($data['temp_user']);

        $rs_data = urlencode(serialize($data));

        if($push) {
            $cmd = 'php ' . FCPATH . 'index.php Messages async_send_push';

            $push_user_list_chunk = array_chunk($users, $this->push_chunk);

            foreach ($push_user_list_chunk as $push_users_chunk) {
                $parameter = $id . ' ' . urlencode(serialize($push_users_chunk)) . ' ' . $rs_data;

                if (substr(php_uname(), 0, 7) == 'Windows') {
                    pclose(popen('start /B ' . $cmd . ' ' . $parameter, 'r'));
                } else {
                    exec($cmd . ' ' . $parameter . ' > /dev/null &');
                }
            }
        } else {
            $cmd = 'php ' . FCPATH . 'index.php Messages async_send_sms';

            $sms_user_list_chunk = array_chunk($users, $this->message_chunk);
            
            foreach ($sms_user_list_chunk as $sms_users_chunk) {
                $parameter = $id . ' ' . urlencode(serialize($sms_users_chunk)) . ' ' . $rs_data;
                
                if (substr(php_uname(), 0, 7) == 'Windows') {
                    pclose(popen('start /B ' . $cmd . ' ' . $parameter, 'r'));
                } else {
                    exec($cmd . ' ' . $parameter . ' > /dev/null &');
                }
            }
        }
    }

    protected function after_insert_data($id, $data)
    {
        $this->load->model('MessageSender');
        $this->MessageSender->insert(array('message_id' => $id, 'admin_id' => $this->session->userdata('admin_id'), 'phone_number' => $data['sender']));

        $sms_users = array();
        $push_users = array();

        switch ($data['type']) {
            case 'push':
                if (empty($data['send_all'])) {
                    if (!empty($data['user'])) {
                        $push_users = $this->{$this->model}->get_push_user($data['user'],$this->input->post('not_user'));
                    }
                } else {
                    $push_users = $this->{$this->model}->get_push_user();
                }
                break;
            case 'wapos':
                if (empty($data['send_all'])) {
                    if(empty($data['user'])) {
                        $push_users = array();
                    } else {
                        $push_users = $this->{$this->model}->get_push_user($data['user']);
                    }

                    $users = array();

                    if (!empty($data['user'])) {
                        if (count($push_users)) {
                            foreach ($push_users as $push_user) {
                                foreach ($data['user'] as $key => $user) {
                                    if ($push_user['user_id'] == $user) {
                                        unset($data['user'][$key]);
                                    }
                                }
                            }
                        }

                        if (!empty($data['user'])) {
                            $users = $this->{$this->model}->get_sms_user($data['user'],$this->input->post('not_user'));
                        }
                    }

                    $temp_users = array();
                    if (!empty($data['temp_user'])) {
                        $temp_users = $this->{$this->model}->get_sms_temp_user($data['temp_user'],$this->input->post('not_temp_user'));
                    }

                    $sms_users = array_merge($users, $temp_users);
                } else {
                    $push_users = $this->{$this->model}->get_push_user(null,$this->input->post('not_user'));
                    $sms_users = $this->{$this->model}->get_sms_user(null,$this->input->post('not_user'));

                    if (count($sms_users)) {
                        if (count($push_users)) {
                            foreach ($push_users as $push_user) {
                                foreach ($sms_users as $index => $sms_user) {
                                    if ($push_user['user_id'] == $sms_user['user_id']) {
                                        unset($sms_users[$index]);
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            default:
                if (empty($data['send_all'])) {
                    $users = array();
                    if (!empty($data['user'])) {
                        $users = $this->{$this->model}->get_sms_user($data['user'],$this->input->post('not_user'));
                    }

                    $temp_users = array();
                    if (!empty($data['temp_user'])) {
                        $temp_users = $this->{$this->model}->get_sms_temp_user($data['temp_user'],$this->input->post('not_temp_user'));
                    }

                    $sms_users = array_merge($users, $temp_users);
                } else {
                    $sms_users = $this->{$this->model}->get_sms_user(null,$this->input->post('not_user'));
                }
        }

        if (count($push_users)) {
            if (count($push_users) >= $this->push_chunk) {
                $this->after_insert_async($id, $push_users, $data, true);
            } else {
                $data['complete_user'] = $push_users;
                $this->send_push($push_users, $data, $data['push_key']);
                $this->after_insert_data_message($id, $data, true);
            }
        }

        if (count($sms_users)) {
            $data['testmode_yn'] = 'Y';
            
            if (ENVIRONMENT == 'production') {
                $data['testmode_yn'] = 'N';
            }

            if (count($sms_users) >= $this->message_chunk) {
                $this->after_insert_async($id, $sms_users, $data);
            } else {
                $data['sms_result'] = $this->send_sms($sms_users, $data);
                $data['complete_user'] = $sms_users;
                $this->after_insert_data_message($id, $data);
            }
        }

        return true;
    }

    public function async_send_sms($id, $sms_users, $data)
    {
        $sms_users = unserialize(urldecode($sms_users));
        $data = unserialize(urldecode($data));
/*
        require APPPATH.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
        $log = new Monolog\Logger('name');
        $log->pushHandler(new Monolog\Handler\StreamHandler(APPPATH.'logs/log.txt', Monolog\Logger::INFO));
        $log->addInfo('excute complete!'); */
        
        $data['sms_result'] = $this->send_sms($sms_users, array('title' => $data['title'], 'sender' => $data['sender'], 'content' => $data['content'], 'attach_file' => $data['attach_file'], 'sms_key' => $data['sms_key'], 'testmode_yn' => $data['testmode_yn'], 'branch_id' => $data['branch_id'], 'admin_id' => $data['admin_id']));
        $data['complete_user'] = $sms_users;

        return $this->after_insert_data_message($id, $data);
    }

    public function async_send_push($id, $push_users, $data)
    {
        $push_users = unserialize(urldecode($push_users));
        $data = unserialize(urldecode($data));
/*
        require APPPATH.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
        $log = new Monolog\Logger('name');
        $log->pushHandler(new Monolog\Handler\StreamHandler(APPPATH.'logs/log.txt', Monolog\Logger::INFO));
        $log->addInfo('excute complete!'); */
        
        $this->send_push($push_users,$data,$data['push_key']);
        $data['complete_user'] = $push_users;

        return $this->after_insert_data_message($id, $data, true);
    }

    private function after_insert_data_message($id, $data, $push=false)
    {
        if (empty($data['admin_id'])) {
            $data['admin_id'] = $this->session->userdata('admin_id');
        }

        if (empty($data['branch_id'])) {
            $data['branch_id'] = $this->session->userdata('branch_id');
        }

        $type='sms';

        if($push) {
            $type='push';
        }

        if (empty($data['send_all'])) {
            $this->load->model('MessageUser');
            $this->load->model('MessageTempUser');

            if (!empty($data['complete_user'])) {
                foreach ($data['complete_user'] as $value) {
                    if (empty($value['user_id'])) {
                        $this->MessageTempUser->insert(array('message_id' => $id, 'temp_user_id' => $value['temp_user_id']));
                    } else {
                        $this->MessageUser->insert(array('message_id' => $id, 'user_id' => $value['user_id'], 'type'=>$type));
                    }
                }
            }
        }

        if (isset($data['attach_file'])) {
            $this->message_file_insert($id, $data['attach_file']);
        }

        if (isset($data['sms_result'])) {
            if (count($data['sms_result'])) {
                $this->load->model('Branch');
                foreach ($data['sms_result'] as $sms_result) {
                    $this->Branch->update_point($sms_result, $data['branch_id']);
                }

                $this->load->model('MessageSmsResult');
                foreach ($data['sms_result'] as $sms_result) {
                    $success_cnt = 0;
                    $error_cnt = 0;
                    $msg_type = null;
                    $msg_id = null;

                    if (isset($sms_result->success_cnt)) {
                        $success_cnt = $sms_result->success_cnt;
                    }

                    if (isset($sms_result->error_cnt)) {
                        $error_cnt = $sms_result->error_cnt;
                    }

                    if (isset($sms_result->msg_type)) {
                        $msg_type = $sms_result->msg_type;
                    }

                    if (isset($sms_result->msg_id)) {
                        $msg_id = $sms_result->msg_id;
                    }

                    $this->MessageSmsResult->insert(array('message_id' => $id, 'result_code' => $sms_result->result_code, 'message' => $sms_result->message, 'msg_id' => $msg_id, 'success_cnt' => $success_cnt, 'error_cnt' => $error_cnt, 'msg_type' => $msg_type));
                }
            }
        }

        return true;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('sender', _('Sender'), 'max_length[20]');
        $this->form_validation->set_rules('type', _('Message Type'), 'required|in_list[push,sms,wapos]|callback_validate_sender');
        $this->form_validation->set_rules('detail_type', _('Type'), 'required|in_list[push,sms,lms,mms]|callback_validate_sms_aq[' . $this->input->post('type') . ']');
        $this->form_validation->set_rules('title', _('Title'), 'required|min_length[2]|max_length[60]|trim');
        $this->form_validation->set_rules('content', _('Content'), 'required|trim');

        if ($this->input->post('temp_user')) {
            $this->form_validation->set_rules('temp_user[]', _('Receiver'), 'callback_validate_user[' . $this->input->post('type') . ']');
        }

        if ($this->input->post('user')) {
            $this->form_validation->set_rules('user[]', _('Receiver'), 'callback_validate_user[' . $this->input->post('type') . ']');
        }
    }

    public function validate_user($n_user, $type)
    {
        $this->load->model($this->model);

        if ($type == 'push') {
            if (!$this->input->post('user')) {
                return false;
            }

            $users = $this->{$this->model}->get_push_user($this->input->post('user'),$this->input->post('not_user'));
            if (empty($users)) {
                return false;
            }
        } else {
            if (!$this->input->post('user') and !$this->input->post('temp_user')) {
                return false;
            }

            if ($this->input->post('user')) {
                $users = $this->{$this->model}->get_sms_user($this->input->post('user'),$this->input->post('not_user'));
            }

            if ($this->input->post('temp_user')) {
                $temp_users = $this->{$this->model}->get_sms_temp_user($this->input->post('temp_user'),$this->input->post('not_temp_user'));
            }

            if(empty($users) and empty($temp_users)) {
                return false;
            }
        }

        return true;
    }

    public function validate_sender($type)
    {
        if ($type == 'push') {
            return true;
        }

        if (empty($this->return_data['common_data']['branch']['phone'])) {
            return false;
        }

        return true;
    }

    public function validate_sms_aq($detail_type, $type)
    {
        if ($type == 'push') {
            return true;
        }

        $this->load->model('Branch');
        $branch = $this->Branch->get_content($this->session->userdata('branch_id'));
        $sms_available_point = $branch['sms_available_point'];

        if (empty($sms_available_point)) {
            $this->form_validation->set_message('validate_sms_aq', _('The sms_available_quantity must greater than 0'));

            return false;
        }

        $use_sms_quantity = $this->get_sms_user_count($type);

        if ($type == 'sms') {
            if (empty($use_sms_quantity)) {
                $this->form_validation->set_message('validate_sms_aq', _('Not selected available user'));

                return false;
            }
        }

        if ($detail_type == 'sms') {
            $fee = SMS_FEE['sms'];
        } elseif ($detail_type == 'lms') {
            $fee = SMS_FEE['lms'];
        } else {
            $fee = SMS_FEE['mms'];
        }

        if (($sms_available_point - ($use_sms_quantity * $fee)) < 0) {
            $this->form_validation->set_message('validate_sms_aq', sprintf(_('Send SMS Point is %.1f(SMS type %s : %.1f * Send Counter : %d),But You Have %.1f'), $use_sms_quantity * $fee, $type, $fee, $use_sms_quantity, $sms_available_point));

            return false;
        } else {
            if ($this->check_available_sms($type, $use_sms_quantity)) {
                return true;
            } else {
                $this->form_validation->set_message('validate_sms_aq', _('Send SMS Fail'));
                return false;
            }
        }
    }

    protected function get_view_data($id)
    {
        $content = $this->get_view_content_data($id);

        $this->load->model('MessageSmsResult');
        $content['msr_content'] = $this->MessageSmsResult->get_content_by_parent_id($content['id']);

        $this->load->model('MessageUser');
        $this->load->model('MessageTempUser');

        $this->MessageUser->parent_id = $content['id'];
        $content['message_users'] = $this->MessageUser->get_index();

        $this->MessageTempUser->parent_id = $content['id'];
        $content['message_temp_users'] = $this->MessageTempUser->get_index();

        return $content;
    }

    protected function get_sms_user_count($type)
    {
        if ($this->input->get_post('search') or $this->input->get_post('counsel_search')) {
            if ($this->input->get_post('counsel_search')) {
                $search_users = $this->get_counsel_user();

                $user = array();
                $temp_user = array();

                if ($search_users['total']) {
                    foreach ($search_users['list'] as $search_user) {
                        if (empty($search_user['user_id'])) {
                            $temp_user[] = $search_user['temp_user_id'];
                        } else {
                            $user[] = $search_user['user_id'];
                        }
                    }
                }

                $this->load->model($this->model);

                $use_sms_user_quantity = 0;
                $use_sms_temp_user_quantity = 0;

                if (count($user)) {
                    $use_sms_user_quantity = count($this->{$this->model}->get_sms_user($user,$this->input->post('not_user')));
                }

                if (count($temp_user)) {
                    $use_sms_temp_user_quantity = count($this->{$this->model}->get_sms_temp_user($temp_user,$this->input->post('not_temp_user')));
                }

                $sms_user_count = $use_sms_user_quantity + $use_sms_temp_user_quantity;
                $m_total = 0;
            }

            if ($this->input->get_post('search')) { // 검색일때
                $search_users = $this->get_search_user();

                $user = array();

                if ($search_users['total']) {
                    foreach ($search_users['list'] as $search_user) {
                        $user[] = $search_user['id'];
                    }
                }

                $m_total = 0;
                if ($type == 'wapos') {
                    $this->load->model($this->model);
                    $m_total = count($this->{$this->model}->get_push_user($user,$this->input->post('not_user')));
                }

                $sms_user_count = $search_users['total'];
            }

        } else { // 일반일때
            if ($this->input->post('send_all')) {
                $this->load->model('User');
                $this->User->phone_only = true;
                $sms_user_count = $this->User->get_count();

                $m_total = 0;
                if ($type == 'wapos') {
                    $this->User->phone_only = false;
                    $this->User->push = true;

                    $m_total = $this->User->get_count();
                }
            } else {
                $this->load->model($this->model);

                $use_sms_user_quantity = 0;
                $use_sms_temp_user_quantity = 0;

                if ($this->input->post('user')) {
                    $use_sms_user_quantity = count($this->{$this->model}->get_sms_user($this->input->post('user'),$this->input->post('not_user')));
                }

                if ($this->input->post('temp_user')) {
                    $use_sms_temp_user_quantity = count($this->{$this->model}->get_sms_temp_user($this->input->post('temp_user'),$this->input->post('not_temp_user')));
                }

                $sms_user_count = $use_sms_user_quantity + $use_sms_temp_user_quantity;

                $m_total = 0;
                if ($type == 'wapos') {
                    if ($this->input->post('user')) {
                        $m_total = count($this->{$this->model}->get_push_user($this->input->post('user')));
                    }
                }
            }
        }

        return $sms_user_count - $m_total;
    }

    public function get_check_remain()
    {
        $branch_key_content = $this->get_sms_key();
        $response = $this->check_remain_api($branch_key_content['sms_id'], $branch_key_content['sms_key']);

        if ($this->format == 'json') {
            echo json_encode((array)$response);
        } else {
            return $response;
        }
    }

    protected function check_remain_api($sms_id, $sms_key)
    {
        $client = new GuzzleHttp\Client(['verify' => false]);
        $response = $client->request('POST', 'https://apis.aligo.in/remain/', array('form_params' => array('userid' => $sms_id, 'key' => $sms_key)));

        return json_decode($response->getBody());
    }

    protected function check_available_sms($type, $count)
    {
        if (ENVIRONMENT != 'production') {
            return true;
        }

        if ($type == 'push') {
            return true;
        }

        $response = $this->get_check_remain();
        // Parse the response object, e.g. read the headers, body, etc.
        // $headers = $response->getHeaders();
        switch ($type) {
            case 'lms':
                if ($response->LMS_CNT >= $count) {
                    return true;
                }
                break;
            case 'mms':
                if ($response->MMS_CNT >= $count) {
                    return true;
                }
                break;
            default : 
                if ($response->SMS_CNT >= $count) {
                    return true;
                }
        }         
        
        return false;
    }

    protected function get_error_messages()
    {
        $message = parent::get_error_messages();
        $message['validate_sender'] = _('The branch must has phone number');
        $message['validate_user'] = _('Invaild User Or User Empty');

        return $message;
    }


    // 메세지 수정은 불가,  메세지 삭제도 실질적으로 삭제 안함: Model/Message/delete 참조
    public function edit($id = null)
    {
        show_404();
    }
}
