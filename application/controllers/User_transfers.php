<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Ac_sync.php';
require_once 'Validate_person.php';

class User_transfers extends SL_Controller
{
    use Ac_sync;
    use Validate_person;
    protected $parent_model = 'User';
    protected $model = 'UserTransfer';
    protected $permission_controller = 'users';
    protected $directory = 'user';
    protected $script = 'user-transfers/index.js';

    protected function permission_check()
    {
        parent::permission_check();

        if ($this->session->userdata('role_id') > 3) {
            show_error('You do not have access to this section');
            exit;
        }
    }

    protected function set_add_form_data()
    {
        $parent_id = $this->uri->segment(3);

        $this->load->model($this->parent_model);
        $content = $this->{$this->parent_model}->get_content($parent_id);

        $this->load->model('Branch');
        $this->Branch->not_branch_id = [1,$this->session->userdata('branch_id')];

        $branch_ids=$this->get_branch_ids();
        $this->Branch->id = $branch_ids;
        $branch_list = $this->Branch->get_index(1000, 0);

        $this->load->model('Enroll');
        $this->Enroll->user_id = $content['id'];
        $this->Enroll->get_not_end_only = true;
        $enroll_list = $this->Enroll->get_index(1000, 0);

        $this->load->model('Rent');
        $this->Rent->user_id = $content['id'];
        $this->Rent->get_not_end_only = true;
        $rent_list = $this->Rent->get_index(1000, 0);

        $this->load->model('RentSw');
        $this->RentSw->user_id = $content['id'];
        $this->RentSw->get_not_end_only = true;
        $rent_sw_list = $this->RentSw->get_index(1000, 0);

        $content['content'] = sprintf(_('User Transfer From Branch %s Person'), $this->session->userdata('branch_name'));

        $this->return_data['data']['branch_list'] = $branch_list;
        $this->return_data['data']['enroll_list'] = $enroll_list;
        $this->return_data['data']['rent_list'] = $rent_list;
        $this->return_data['data']['rent_sw_list'] = $rent_sw_list;
        $this->return_data['data']['content'] = $content;
    }

    protected function set_insert_data($data)
    {
        $parent_id = $this->uri->segment(3);

        $data['user_id'] = $parent_id;
        $data['old_branch_id'] = $this->session->userdata('branch_id');
        $data['new_branch_id'] = $data['branch_id'];

        return $data;
    }

    protected function after_insert_data($id, $data)
    {
        $user_info_content = $this->user_tansfer($id, $data);

        if (!empty($data['new_enroll'])) {
            $this->enroll_transfer($data['new_branch_id'], $user_info_content['new_user_id'], $data['new_enroll']);
        }

        if (!empty($data['new_rent'])) {
            $this->rent_transfer($data['new_branch_id'], $user_info_content['new_user_id'], $data['new_rent']);
        }

        if (!empty($data['new_rent_sw'])) {
            $this->rent_sw_transfer($data['new_branch_id'], $user_info_content['new_user_id'], $data['new_rent_sw']);
        }

        $this->change_enddate_current_order($user_info_content['old_user_id']);
        $this->change_enddate_current_order($user_info_content['old_user_id'],true);        

        $this->sync_access_controller($user_info_content['old_user_id']);
        $this->sync_access_controller($user_info_content['new_user_id']);
    }

    protected function new_user_stop($stop_data) {
        $this->load->model('UserStop');
        return $this->UserStop->insert($stop_data);
    }

    protected function new_order($order_data)
    {
        $this->load->model('Order');
        $new_order_id = $this->Order->insert($order_data);

        $this->load->model('OrderProduct');
        $this->OrderProduct->insert(array('order_id' => $new_order_id, 'product_id' => $order_data['product_id']));

        return $new_order_id;
    }

    protected function new_account(array $new_content, $old_order_id)
    {
        $this->load->model('Account');
        $this->load->model('AccountOrder');

        $this->AccountOrder->order_id = $old_order_id;
        $this->AccountOrder->no_commission = true;
        $this->AccountOrder->no_branch_transfer = true;

        $account_list = $this->AccountOrder->get_index();

        if (empty($account_list['total'])) {
            return true;
        }

        foreach ($account_list['list'] as $account) {
            $account['branch_id'] = $new_content['branch_id'];
            $account['user_id'] = $new_content['user_id'];
            $account['account_category_id'] = BRANCH_TRANSFER;
            $account['order_id'] = $new_content['order_id'];
            $account['product_id'] = $new_content['product_id'];

            $this->Account->insert($account);
        }
    }

    protected function stop_transfer($new_order_id, $new_user_id, $stop_list)
    {
        $this->load->model('UserStop');
        $this->load->model('OrderStop');
        $this->load->model('OrderStopLog');
        $this->load->model('OrderStopLogOrderStop');

        foreach ($stop_list['list'] as $index => $value) {
            if (empty($value)) {
                continue;
            }

            $user_stop_content=$value;
            $user_stop_content['user_id']=$new_user_id;
            $user_stop_content['order_id']=$new_order_id;
            
            $new_user_stop_id = $this->new_user_stop($user_stop_content);

            $this->OrderStopLog->user_stop_id=$value['id'];


            if (empty($this->OrderStopLog->get_count())) {
                continue;
            }
            
            $order_stop_log_list=$this->OrderStopLog->get_index();
            
            foreach($order_stop_log_list['list'] as $order_stop_log) {
                if($order_stop_log['order_id']!=$value['order_id']) {
                    continue;
                }

                $order_stop_content=$this->OrderStop->get_content($order_stop_log['order_stop_id']);
                
                $order_stop_content['order_id']=$new_order_id;
                $order_stop_content['user_stop_id']=$new_user_stop_id;

                $order_stop_log_content=$this->OrderStopLog->get_content($order_stop_log['id']);
                $order_stop_log_content['order_id']=$new_order_id;
                
                $order_stop_id=$this->OrderStop->insert($order_stop_content);
                $order_stop_log_id=$this->OrderStopLog->insert($order_stop_log_content);
                $this->OrderStopLogOrderStop->insert(array('order_stop_id'=>$order_stop_id,'order_stop_log_id'=>$order_stop_log_id));
            }
        }
    }

    protected function enroll_transfer($new_branch_id, $new_user_id, $order_course_list)
    {
        $this->load->model('Enroll');
        $this->load->model('Course');
        $this->load->model('UserStop');

        foreach ($order_course_list as $key => $value) {
            if (empty($value)) {
                continue;
            }

            $enroll_content = $this->Enroll->get_content($key);
            $old_user_id=$enroll_content['user_id'];

            $course_content = $this->Course->get_content($value);
            $enroll_content['course_id'] = $course_content['id'];
            $enroll_content['product_id'] = $course_content['product_id'];

            $enroll_content['branch_id'] = $new_branch_id;
            $enroll_content['user_id'] = $new_user_id;

            $old_order_id = $enroll_content['order_id'];
            $enroll_content['order_id'] = $this->new_order($enroll_content);

            $this->Enroll->insert($enroll_content);
            $this->new_account($enroll_content, $old_order_id);

            $this->UserStop->user_id=$old_user_id;
            $this->UserStop->order_id=$old_order_id;

            $stop_count=$this->UserStop->get_count();
            
            if(!empty($stop_count)) {
                $stop_lists=$this->UserStop->get_index();
                $this->stop_transfer($enroll_content['order_id'],$new_user_id,$stop_lists);
            }
        }
    }

    protected function rent_transfer($new_branch_id, $new_user_id, $order_facility_list)
    {
        $this->load->model('Rent');
        $this->load->model('Facility');

        foreach ($order_facility_list as $key => $value) {
            if (empty($value)) {
                continue;
            }

            $rent_content = $this->Rent->get_content($key);

            $facility_content = $this->Facility->get_content($value);
            $rent_content['facility_id'] = $facility_content['id'];
            $rent_content['product_id'] = $facility_content['product_id'];

            $rent_content['branch_id'] = $new_branch_id;
            $rent_content['user_id'] = $new_user_id;
            $rent_content['no'] = 0;

            $old_order_id = $rent_content['order_id'];
            $rent_content['order_id'] = $this->new_order($rent_content);

            $this->Rent->insert($rent_content);
            $this->new_account($rent_content, $old_order_id);
        }
    }

    protected function rent_sw_transfer($new_branch_id, $new_user_id, $order_product_list)
    {
        $this->load->model('RentSw');

        foreach ($order_product_list as $key => $value) {
            if (empty($value)) {
                continue;
            }

            $rent_sw_content = $this->RentSw->get_content($key);
            $rent_sw_content['branch_id'] = $new_branch_id;
            $rent_sw_content['user_id'] = $new_user_id;
            $rent_sw_content['product_id'] = $value;

            $old_order_id = $rent_sw_content['order_id'];
            $rent_sw_content['order_id'] = $this->new_order($rent_sw_content);

            $this->RentSw->insert($rent_sw_content);
            $this->new_account($rent_sw_content, $old_order_id);
        }
    }

    protected function user_tansfer($id, $data)
    {
        $this->load->model($this->parent_model);
        $content = $this->{$this->parent_model}->get_content($data['user_id']);

        $content['branch_id'] = $data['branch_id'];
        $content['updated_at'] = $this->now;

        $new_user_id = $this->{$this->parent_model}->insert($content);

        if (empty($new_user_id)) {
            throw new Exception('Error');
        }

        $this->load->model('Branch');
        $branch_content = $this->Branch->get_content($data['new_branch_id']);

        if ($branch_content['use_access_card']) {
            $dec_only = false;

            $this->load->model('AccessController');
            $this->AccessController->controller = 'ist';
            $ac_count = $this->AccessController->get_count();

            if ($ac_count) {
                $dec_only = true;
            }

            $card_no=$this->create_card_no($content['phone'],$dec_only,$data['new_branch_id']);
            
            $this->load->model('UserAccessCard');
            $this->UserAccessCard->insert(array('user_id' => $new_user_id, 'card_no' => $card_no));
        }

        $this->load->model('UserAdditional');
        $user_a_content = $this->UserAdditional->get_content_by_parent_id($data['user_id']);

        if ($user_a_content) {
            $user_a_content['user_id'] = $new_user_id;
            $this->UserAdditional->insert($user_a_content);
        }

        $this->load->model('UserHeight');
        $user_h_content = $this->UserHeight->get_content_by_parent_id($data['user_id']);

        if ($user_h_content) {
            $user_h_content['user_id'] = $new_user_id;
            $this->UserHeight->insert($user_h_content);
        }

        $this->load->model('UserWeight');
        $user_bi_content = $this->UserWeight->get_content_by_parent_id($data['user_id']);

        if ($user_bi_content) {
            $user_bi_content['user_id'] = $new_user_id;
            $this->UserWeight->insert($user_bi_content);
        }

        $this->load->model('UserContent');
        $this->UserContent->user_id = $data['user_id'];
        $user_content_list = $this->UserContent->get_index();

        if ($user_content_list['total']) {
            foreach ($user_content_list['list'] as $user_content) {
                $user_content['user_id'] = $new_user_id;
                $this->UserContent->insert($user_content);
            }
        }

        if (!empty($data['content'])) {
            $this->UserContent->insert(array('user_id' => $new_user_id, 'content' => $data['content']));            
        }

        $to_content = sprintf(_('User Transfer To Branch %s Person'), $branch_content['title']);     
        $this->UserContent->insert(array('user_id' => $data['user_id'], 'content' => $to_content));        

        $this->load->model('UserUserTransfer');
        $this->UserUserTransfer->insert(array('user_id' => $new_user_id, 'user_transfer_id' => $id));

        if (!empty($content['picture_url'])) {
            $this->load->model('UserPicture');
            if ($this->UserPicture->insert(array('user_id' => $new_user_id, 'picture_url' => $content['picture_url']))) {
                $this->copy_photo($this->session->userdata('branch_id'), $data['branch_id'], $content['picture_url']);
            }
        }

        return array('old_user_id' => $content['id'], 'new_user_id' => $new_user_id);
    }

    protected function change_enddate_current_order($user_id, $stopped = false)
    {
        $date_time_obj = new DateTime('now', $this->timezone);
        $date_time_obj->modify('-1 Day');
        $yesterday=$date_time_obj->format('Y-m-d');
        $updated_at=$this->now;

        $this->load->model('Order');

        $this->load->model('Enroll');
        $this->Enroll->user_id = $user_id;
        $this->Enroll->get_not_end_only = true;

        if ($stopped) {
            $this->Enroll->stopped = true;
        }

        $enroll_list = $this->Enroll->get_index(1000, 0);

        $this->load->model('OrderEnd');

        if ($enroll_list['total']) {
            foreach ($enroll_list['list'] as $enroll) {
                if($enroll['lesson_type']==4) {
                    $this->Enroll->update(array('quantity'=>$enroll['use_quantity'],'id'=>$enroll['id']));
                }

                $start_date_change=false;

                if(new DateTime($enroll['start_date'], $this->timezone)>=$date_time_obj) {
                    $start_date_change=true;
                }

                $this->Enroll->end($enroll['id'],$yesterday,$start_date_change);
                $this->Order->update(array('updated_at'=>$updated_at,'id'=>$enroll['order_id']));
            }
        }

        $this->load->model('Rent');
        $this->Rent->user_id = $user_id;
        $this->Rent->get_not_end_only = true;

        if ($stopped) {
            $this->Rent->stopped = true;
        }

        $rent_list = $this->Rent->get_index(1000, 0);

        if ($rent_list['total']) {
            foreach ($rent_list['list'] as $rent) {
                $start_date_change=false;

                if(new DateTime($rent['start_datetime'], $this->timezone)>=$date_time_obj) {
                    $start_date_change=true;
                }

                $this->Rent->end($rent['id'],$yesterday.' 23:59:59',$start_date_change);
                $this->Order->update(array('updated_at'=>$updated_at,'id'=>$rent['order_id']));             
            }
        }

        $this->load->model('RentSw');
        $this->RentSw->user_id = $user_id;
        $this->RentSw->get_not_end_only = true;

        if ($stopped) {
            $this->RentSw->stopped = true;
        }

        $rent_sw_list = $this->RentSw->get_index(1000, 0);

        if ($rent_sw_list['total']) {
            foreach ($rent_sw_list['list'] as $rent_sw) {
                $start_date_change=false;

                if(new DateTime($rent_sw['start_date'], $this->timezone)>=$date_time_obj) {
                    $start_date_change=true;
                }

                $this->RentSw->end($rent_sw['id'],$yesterday,$start_date_change);
                $this->Order->update(array('updated_at'=>$updated_at,'id'=>$rent_sw['order_id']));
            }
        }
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('user_id', _('User'), 'required|integer');
        $this->form_validation->set_rules('branch_id', _('Branch'), 'required|integer');
    }

    protected function insert_complete_message($id)
    {
        $content = $this->get_view_content_data($id);

        $this->load->model('Branch');
        $branch_content = $this->Branch->get_content($content['new_branch_id']);

        return sprintf(_('Successfully Transfer %s To %s'), $content['user_name'], $branch_content['title']);
    }

    protected function add_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return '/';
        }
    }

    public function copy_photo($origin_branch, $move_branch, $filename)
    {
        switch ($this->file_server_type) {
            case 'AWS':
                $this->_copy_S3($origin_branch, $move_branch, $filename);
                break;
            case 'AzureRM':
                $this->_copy_azure_blob($origin_branch, $move_branch, $filename);
                break;
            case 'FTP':
                $this->_copy_ftp($origin_branch, $move_branch, $filename);
                break;
            default:
                $this->_copy_file($origin_branch, $move_branch, $filename);
        }
    }

    protected function _copy_S3($origin_branch, $move_branch, $filename)
    {
        return true;
    }

    protected function _copy_ftp($origin_branch, $move_branch, $filename)
    {
        return true;
    }

    protected function _copy_file($origin_branch, $move_branch, $filename)
    {
        $files = array(
            $filename,
            'large_thumb_'.$filename,
            'medium_thumb_'.$filename,
            'small_thumb_'.$filename,
        );

        $origin_path = FCPATH.'files'.DIRECTORY_SEPARATOR.strtolower($this->directory).DIRECTORY_SEPARATOR.$origin_branch;
        $move_path = FCPATH.$this->check_and_make_directory('files', array(strtolower($this->directory), $move_branch));

        foreach ($files as $file) {
            copy($origin_path.DIRECTORY_SEPARATOR.$file, $move_path.DIRECTORY_SEPARATOR.$file);
        }
    }

    protected function _copy_azure_blob($origin_branch, $move_branch, $filename)
    {
        $files = array(
            $filename,
            'large_thumb_'.$filename,
            'medium_thumb_'.$filename,
            'small_thumb_'.$filename,
        );

        try {
            $blobRestProxy = \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobService('DefaultEndpointsProtocol=https;AccountName='.$_ENV['AZURE_STORAGE_ACCOUNT_NAME'].';AccountKey='.$_ENV['AZURE_STORAGE_ACCESS_KEY']);

            foreach ($files as $file) {
                $blobRestProxy->copyBlob($_ENV['FOG_DIRECTORY'], strtolower($this->directory).'/'.$move_branch.'/'.$file, $_ENV['FOG_DIRECTORY'], strtolower($this->directory).'/'.$origin_branch.'/'.$file);
            }

            return true;
        } catch (\MicrosoftAzure\Storage\Common\Exceptions\ServiceException $e) {
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
            $check_directory .= DIRECTORY_SEPARATOR.$value;

            if (!file_exists($check_directory)) {
                if (!mkdir($check_directory)) {
                    throw new Exception($check_directory.' can not make', 1);
                }
            }
        }

        return $check_directory;
    }

    public function delete_confirm($id)
    {
        $this->return_data['data']['id'] = $id;
        $this->layout->render('user_transfers/delete', $this->return_data);
    }

    protected function delete_redirect_path(array $content)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return '/view/'.$content['user_id'];
        }
    }

    protected function delete_complete_message(array $content)
    {
        return _('Successfully Deleted Transfer Log');
    }
}
