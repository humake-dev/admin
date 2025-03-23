<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Ac_sync_admin.php';
require_once 'Validate_person.php';
require_once 'Pagination_aside.php';

class Employees extends SL_Controller
{
    use Ac_sync_admin;
    use Validate_person;
    use Pagination_aside;

    protected $model = 'Employee';
    protected $use_index_content = true;
    protected $script = 'employees/index.js';

    protected function permission_check()
    {
        if (in_array($this->router->fetch_method(), ['select'])) {
            return true;
        }

        parent::permission_check();
    }

    public function index()
    {
        $this->index_data($this->input->get($this->category_id_name));
        $json_array = array_merge(['result' => 'success'], $this->return_data['data']['admin']);
        $this->render_format($json_array);
    }

    protected function index_data($category_id = null)
    {
        $this->common_index();
        $this->get_role_n_permission_data();
    }

    public function view($id = null)
    {
        if (empty($id)) {
            show_404();
        }

        $this->common_index($id);

        $this->get_role_n_permission_data();
        $this->render_format();
    }

    protected function get_role_n_permission_data()
    {
        $this->load->model('Role');
        $this->return_data['data']['role'] = $this->Role->get_show_list(1000, 0);

        $this->load->model('Permission');
        $permission = $this->Permission->get_permission_index(1000, 0);
        
        $this->return_data['data']['permission'] = $permission;
    }

    protected function render_index_resource()
    {
        $this->layout->add_js('timepicki.js');
        $this->layout->add_js($this->script . '?version=' . $this->assets_version);
    }

    public function select($type = 'multi')
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($this->input->get());
        $this->form_validation->set_rules('status', _('Status'), 'in_list[A,H,R,L]');
        $this->form_validation->set_rules('position', _('Employee Position'), 'in_list[all,fc,trainer]');

        if ($this->form_validation->run() == false) {
        } else {
            $this->set_page();

            $position = 'all';
            $default_position = 'all';
            if ($this->input->get('default_position')) {
                $default_position = $this->input->get('default_position');
            }

            if ($this->input->get('position')) {
                $default_position = $this->input->get('position');
                $position = $default_position;
            }

            $this->get_admin_list($default_position, $this->per_page, $this->page);
            if ($this->format == 'json') {
                if ($this->return_data['data']['admin']['total']) {
                    echo json_encode(['result' => 'success', 'total' => $this->return_data['data']['admin']['total'], 'list' => $this->return_data['data']['admin']['list']]);
                } else {
                    echo json_encode(['result' => 'success', 'total' => $this->return_data['data']['admin']['total']]);
                }
            } else {
                if ($type == 'single') {
                    $this->return_data['data']['type'] = 'single';
                } else {
                    $this->return_data['data']['type'] = 'multi';
                }

                $this->setting_pagination(['total_rows' => $this->return_data['data']['admin']['total']]);
                $this->return_data['data']['position'] = $position;
                $this->return_data['data']['per_page'] = $this->per_page;
                $this->return_data['data']['page'] = $this->page;
                $this->script = 'employees/select.js';
                parent::render_index_resource();
                $this->layout->render('employees/select', $this->return_data);
            }
        }
    }

    protected function set_add_form_data($id = null)
    {
        $this->session->unset_userdata('employee_open');
        $this->get_role_n_permission_data();
        $this->return_data['data']['total'] = 0;
    }

    protected function set_edit_form_data(array $content)
    {
        $this->common_index($content['id']);
        $this->get_role_n_permission_data();

        $eesm = $this->check_enable_send_message($content['id']);

        if (!empty($eesm)) {
            $content['enable_send_message'] = 1;
        }

        $this->return_data['data']['total'] = 0;
        $this->return_data['data']['content'] = $content;
        $this->return_data['data']['admin']['content'] = $content;
    }

    protected function render_form_resource()
    {
        $this->render_index_resource();
    }

    public function attendances($id)
    {
        if ($content = $this->common_index($id)) {
            $this->load->model('EmployeeAttendance');
            $this->EmployeeAttendance->employee_id = $id;
            $this->return_data['data']['list'] = $this->EmployeeAttendance->get_index(10, 0);
            $this->return_data['data']['attendance'] = $this->EmployeeAttendance->get_attendance_by_user($id);

            $this->return_data['data']['today_list'] = $this->EmployeeAttendance->get_attendance_by_user_n_date($id, $this->date);
        }

        $this->script = 'employees/attendance.js';
        $this->render_format();
    }

    private function get_oe_list (Array $content,$oe='odd') {
        $users=array();
        $tt=array();
        $t_users=array();    

        $this->per_page=100000;
        $list=$this->get_search_list($content);
                
        foreach($list['list'] as $index=>$value) {
            $tt[]=$value['id'];
        }

        $t_users=array_chunk($tt,10);

        foreach($t_users as $index=>$value) {
            if($oe=='odd') {
                if(($index%2)==1) {
                    continue;
                }
            } else {
                if(($index%2)==0) {
                    continue;
                }
            }

            foreach($value as $u) {
                $users[]=$u;
            }
        }

        return $users;
    }


    private function get_oe_counsel_list (Array $content,$oe='odd') {
        $users=array();
        $tt=array();
        $t_users=array();    

        $this->per_page=100000;
        $list=$this->get_search_counsel_list($content);
                
        foreach($list['list'] as $index=>$value) {
            $tt[]=$value['id'];
        }

        $t_users=array_chunk($tt,10);

        foreach($t_users as $index=>$value) {
            if($oe=='odd') {
                if(($index%2)==1) {
                    continue;
                }
            } else {
                if(($index%2)==0) {
                    continue;
                }
            }

            foreach($value as $u) {
                $users[]=$u;
            }
        }

        return $users;
    }

    public function users($id)
    {
        $this->load->library('form_validation');

        if(!$this->input->post('select_oe')) {
            if($this->input->post('real_all')=='1') {
                $this->form_validation->set_rules('unselect_user_id[]', _('User'), 'callback_check_unselect_user_id');
            } else {
                $this->form_validation->set_rules('select_user_id[]', _('User'), 'callback_check_user_id');
            }
        }
        $this->form_validation->set_rules('change_fc_id', _('Change FC'), 'integer');
        $this->form_validation->set_rules('change_trainer_id', _('Change Trainer'), 'integer');
        $this->set_message();

        if ($this->form_validation->run() == false) {
            if ($this->format == 'json') {
                $this->set_page();

                $content = $this->get_view_content_data($id);
            } else {
                $this->script = 'employees/user.js';
                $this->common_index($id);
                $this->return_data['data']['fc'] = $this->get_admin_list('fc', 1000, 0, false);

                if($this->input->get_post('real_all')) {
                    $this->return_data['data']['real_all'] = true;
                }

                $content = $this->return_data['data']['content'];
            }

            if(empty($content['is_fc'])) {
                redirect('/employees/view/'.$content['id']);
            }

            $list=$this->get_search_list($content);

            $this->return_data['data']['total'] = $list['total'];

            if (!empty($list['total'])) {
                $this->return_data['data']['list'] = $list['list'];
            }

            $this->render_format();
        } else {
            $content = $this->get_view_content_data($id);
            $users=array();
            
            if($this->input->post('real_all')) {
                $this->per_page=100000;
                $list=$this->get_search_list($content,true);
                
                foreach($list['list'] as $value) {
                    $users[]=$value['id'];
                }
            } else {
                if($this->input->post('select_oe')) {
                    $users=$this->get_oe_list($content,$this->input->post('select_oe'));
                }
            }
            
            if($this->input->post('unselect_user_id')) {
                foreach($this->input->post('unselect_user_id') as $unselect_user) {
                    foreach($users as $index=>$user) {
                        if($user== $unselect_user) {
                            unset($users[$index]);
                        }
                    }
                }
            }
            
            if($this->input->post('select_user_id')) {
                foreach($this->input->post('select_user_id') as $select_user) {
                    $users[]=$select_user;
                }
            }

            $this->change_employee_user($users,$content['id']);
            $this->session->set_flashdata('message', ['type' => 'success', 'message' => _('Sucessfully Change User`s Manager')]);
            $redirect_url='/employees/users/'.$id;

            if($this->input->get_post('eu_search')) {
                $params='';

                if($this->input->get_post('status')) {
                    $params.='?status='.$this->input->get_post('status');
                }
                
                if($this->input->get_post('period')) {
                    if ($this->input->get_post('status')) {
                        $params.='&';
                    } else {
                        $params.='?';
                    }
                    $params.='period='.$this->input->get_post('period');
                }

                $redirect_url.=$params;
            }

            redirect($redirect_url);
        }
    }

    public function counsels($id)
    {
        $this->load->library('form_validation');

        if(!$this->input->post('select_oe')) {
            if($this->input->post('real_all')=='1') {
                $this->form_validation->set_rules('unselect_user_id[]', _('User'), 'callback_check_unselect_user_id');
            } else {
                $this->form_validation->set_rules('select_user_id[]', _('User'), 'callback_check_user_id');
            }
        }
        $this->form_validation->set_rules('change_fc_id', _('Change FC'), 'integer');

        $this->load->library('form_validation');

        $this->set_message();

        if ($this->form_validation->run() == false) {
            if ($this->format == 'json') {
                $this->set_page();

                $content = $this->get_view_content_data($id);
            } else {
                $this->script = 'employees/counsel.js';
                $this->common_index($id);
                $this->return_data['data']['fc'] = $this->get_admin_list('fc', $per_page = 1000, $page = 0, false);

                if($this->input->get_post('real_all')) {
                    $this->return_data['data']['real_all'] = true;
                }

                $content = $this->return_data['data']['content'];
            }

            if(empty($content['is_fc'])) {
                redirect('/employees/view/'.$content['id']);
            }

            $list=$this->get_search_counsel_list($content);

            $this->return_data['data']['total'] = $list['total'];

            if ($list['total']) {
                $this->return_data['data']['list'] = $list['list'];
            }

            $this->render_format();
        } else {
            $content = $this->get_view_content_data($id);
            $counsels=array();
            
            if($this->input->post('real_all')) {
                $this->per_page=100000;
                $list=$this->get_search_counsel_list($content,true);
                
                foreach($list['list'] as $value) {
                    $counsels[]=$value['id'];
                }
            } else {
                if($this->input->post('select_oe')) {
                    $counsels=$this->get_oe_counsel_list($content,$this->input->post('select_oe'));
                }
            }
            
            if($this->input->post('unselect_user_id')) {
                foreach($this->input->post('unselect_user_id') as $unselect_counsel) {
                    foreach($counsels as $index=>$counsel) {
                        if($counsel== $unselect_counsel) {
                            unset($counsels[$index]);
                        }
                    }
                }
            }
            
            if($this->input->post('select_user_id')) {
                foreach($this->input->post('select_user_id') as $select_counsel) {
                    $counsels[]=$select_counsel;
                }
            }

            $this->change_counsel_manager($counsels,$content['id']);
            $this->session->set_flashdata('message', ['type' => 'success', 'message' => _('Sucessfully Change User`s Manager')]);
            $redirect_url='/employees/counsels/'.$id;

            if($this->input->get_post('eu_search')) {
                $params='';

                if($this->input->get_post('status')) {
                    $params.='?status='.$this->input->get_post('status');
                }
                
                if($this->input->get_post('period')) {
                    if ($this->input->get_post('status')) {
                        $params.='&';
                    } else {
                        $params.='?';
                    }
                    $params.='period='.$this->input->get_post('period');
                }

                $redirect_url.=$params;
            }

            redirect($redirect_url);
        }
    }

    private function get_search_list($content,$id_only=false)
    {
        $this->load->model('EmployeeUser');

        if (empty($content['is_fc']) and empty($content['is_trainer'])) {
            $list['total'] = 0;
        } else {
            if (!empty($content['is_trainer'])) {
                $this->EmployeeUser->trainer_id = $content['id'];
            }

            if (!empty($content['is_fc'])) {
                $this->EmployeeUser->fc_id = $content['id'];
            }

            if($this->input->get_post('status') or $this->input->get_post('quantity') or $this->input->get_post('period')) {
                if($this->input->get_post('status')) {
                    $this->EmployeeUser->status=$this->input->get_post('status');
                }
            
                if($this->input->get_post('period')) {
                    $this->EmployeeUser->period=$this->input->get_post('period');
                }

                $this->return_data['data']['search']=1;
            }

            if(!empty($id_only)) {
                $this->EmployeeUser->id_only=true;
            }

            $list = $this->EmployeeUser->get_index($this->per_page, $this->page);
        }
        
        if(empty($list['total'])) {
            return $list;
        }
        
        if(empty($id_only)) {
            foreach($list['list'] as $index=>$value) {
                $oo=get_employee_user_oo($list['list'][$index]['oo']);
                if(empty($oo)) {
                        $list['list'][$index]['period']='';
                        $list['list'][$index]['account']='';
                        $list['list'][$index]['transaction_date']='';
                } else {
                        $list['list'][$index]['period']=$oo['period'];
                        $list['list'][$index]['account']=$oo['account'];
                        $list['list'][$index]['transaction_date']=get_dt_format($oo['transaction_date'],$this->timezone); 
                }
            }
        }
        
        return $list;
    }

    private function get_search_counsel_list($content,$id_only=false)
    {
        $this->load->model('Counsel');

        if (empty($content['is_fc']) and empty($content['is_trainer'])) {
            $list['total'] = 0;
        } else {
            if (!empty($content['is_fc'])) {
                $this->Counsel->search['manager'] = $content['id'];
            }

            if(in_array($this->input->get_post('complete'),array(0,1)) or $this->input->get_post('type')) {
                if(in_array($this->input->get_post('complete'),array(0,1))) {
                    $this->Counsel->search['complete'] = $this->input->get_post('complete');
                }

                if($this->input->get_post('type')) {
                    $this->Counsel->search['type'] = $this->input->get_post('type');
                }

                $this->return_data['data']['search']=1;
            }

            if(!empty($id_only)) {
                $this->Counsel->id_only=true;
            }

            $list = $this->Counsel->get_index($this->per_page, $this->page);
        }
        
        if(!empty($list['total'])) {
            if($this->format == 'json') {
                foreach($list['list'] as $index => $value) {
                    $list['list'][$index]['execute_date'] = get_dt_format($value['execute_date'], $this->timezone);
                }
            }
        }
        
        return $list;
    }

    private function change_employee_user(Array $users,$employee_id)
    {
        $fc_id = $this->input->post('change_fc_id');

        $this->load->model('UserFc');
        foreach ($users as $user) {
            $this->UserFc->change_fc($fc_id, $employee_id, $user);
        }
    }
    
    private function change_counsel_manager(Array $counsels,$employee_id)
    {
        $this->load->model('CounselManager');
        foreach ($counsels as $counsel) {
            $fc_id = $this->input->post('change_fc_id');
            $this->CounselManager->change_fc($fc_id, $employee_id, $counsel);
        }
    }

    private function common_index($id = null)
    {
        $this->set_page();

        $type = 'all';
        if ($this->input->get('trainer')) {
            $type = 'trainer';
        }

        $admin = $this->get_admin_list($type, $this->per_page, $this->page);

        if ($this->input->get('search_type')) {
            $this->return_data['search_data']['search_type'] = $this->input->get('search_type');
        }

        if (empty($id)) {
            $content = $this->get_list_view_data($admin);
        } else {
            $content = $this->get_view_content_data($id);
        }

        if ($this->router->fetch_method() == 'index') {
            $this->setting_pagination(['base_url' => base_url() . 'employees', 'total_rows' => $admin['total']]);
        } else {
            if ($this->router->fetch_method() == 'view') {
                $this->setting_pagination(['base_url' => base_url() . $this->router->fetch_class() . '/view/' . $id, 'total_rows' => $admin['total']]);
            } else {
                $this->setting_pagination(['base_url' => base_url() . $this->router->fetch_class() . '/' . $this->router->fetch_method() . '/' . $id, 'total_rows' => $admin['total']]);
            }
        }

        if(empty($content)) {
            return $content;
        } else {
            $this->return_data['data']['content'] = $content;
            $this->return_data['data']['admin']['content'] = $content;
        }

        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->return_data['data']['role_permission'] = $this->Acl->role_permissions($content['role_id']);
        $this->return_data['data']['total'] = 0;

        return $content;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('uid', _('uid'), 'required|trim|max_length[40]|callback_unique_uid[' . $id . ']');
        $this->form_validation->set_rules('email', _('Email'), 'trim|valid_email|callback_unique_email[' . $id . ']');
        $this->form_validation->set_rules('name', _('Name'), 'required|trim|min_length[2]|max_length[60]');
        $this->form_validation->set_rules('phone', _('Phone'), 'min_length[4]|max_length[15]|callback_numeric_dash');
        $this->form_validation->set_rules('gender', _('Gender'), 'integer');
        $this->form_validation->set_rules('birthday', _('Birthday'), 'callback_valid_date');
        $this->form_validation->set_rules('is_trainer', _('Trainer'), 'integer');
        $this->form_validation->set_rules('is_fc', _('FC'), 'integer');
        $this->form_validation->set_rules('role', _('Role'), 'required|integer');
        $this->form_validation->set_rules('commission_rate', _('Commission'), 'integer|less_than_equal_to[100]');
        $this->form_validation->set_rules('status', _('Status'), 'required|in_list[H,R,L]');

        if ($this->router->fetch_method() == 'add') {
            $this->form_validation->set_rules('password', _('Password'), 'required|min_length[4]|max_length[40]');
        }

        if ($this->return_data['common_data']['branch']['use_admin_ac']) {
            $this->form_validation->set_rules('card_no', _('Access Card No'), 'trim|numeric|min_length[10]|max_length[10]|callback_unique_card_no[' . $id . ']');
        }
    }

    public function unique_uid($uid, $id = null)
    {
        $this->load->model($this->model);

        return $this->{$this->model}->check_unique_uid($uid, $id);
    }

    public function unique_email($email, $id = null)
    {
        if (empty(trim($email))) {
            return true;
        }

        $this->load->model($this->model);

        return $this->{$this->model}->check_unique_email($email, $id);
    }

    protected function get_error_messages()
    {
        $message = parent::get_error_messages();
        $message['unique_uid'] = $message['is_unique'];
        $message['unique_email'] = $message['is_unique'];
        $message['numeric_dash'] = _('The %s field allow only number and dash');
        $message['check_user_id'] = _('The %s field must be numeric array.');

        return $message;
    }

    protected function set_insert_data($data)
    {
        if (!empty($data['password'])) {
            $data['pwd'] = $data['password'];
            $data['encrypted_password'] = crypt($data['password'] . $this->config->item('encryption_key'), '$2a$10$' . substr(md5(time()), 0, 22));
            unset($data['password']);
        }

        $data['name'] = trim($data['name']);
        $data['phone'] = $this->create_valid_phone($data['phone']);

        $data['role_id'] = $data['role'];

        if (empty($data['gender'])) {
            $data['gender'] = 0;
        }

        if (empty($data['is_fc'])) {
            $data['is_fc'] = 0;
        }

        if (empty($data['is_trainer'])) {
            $data['is_trainer'] = 0;
        }

        if (empty($data['hiring_date'])) {
            $data['hiring_date'] = $this->date;
        }

        if (empty($data['birthday'])) {
            $data['birthday'] = null;
        }

        if (empty($data['email'])) {
            $data['email'] = null;
        }

        if ($this->return_data['common_data']['branch']['use_admin_ac']) {
            if (empty($data['card_no'])) { // 카드번호가 없으면 만들어 넣는다.
                $data['card_no'] = $this->create_card_no($data['phone'], true);
            }
        }

        return $data;
    }

    protected function set_update_data($id, $data)
    {
        $data = $this->set_insert_data($data);
        $data['id'] = $id;

        return $data;
    }

    protected function after_update_data($id, $data)
    {
        if ($this->return_data['common_data']['branch']['use_admin_ac']) {
            if (!empty($data['card_no'])) {
                $this->load->model('EmployeeAccessCard');
                $this->EmployeeAccessCard->insert(['admin_id' => $id, 'card_no' => $data['card_no']]);
            }

            $this->sync_access_controller($id);
        }

        $eesm_id = $this->check_enable_send_message($id);

        if (empty($data['enable_send_message'])) {
            if ($eesm_id) {
                $this->EmployeeEnableSendMessage->delete($eesm_id);
            }
        } else {
            if ($eesm_id) {
                $this->EmployeeEnableSendMessage->update(['admin_id' => $id, 'id' => $eesm_id]);
            } else {
                $this->EmployeeEnableSendMessage->insert(['admin_id' => $id]);
            }
        }

        if (empty($data['admin_permission'])) {
            return true;
        }

        if($data['edit_content']['role_id']!=$data['role_id']) {
            return $this->remove_all_admin_controller_permission($id);
        } 

        $change_permissions = [];
        foreach ($data['admin_permission'] as $user_permission) {
            $current_permission = $this->Acl->has_permission($user_permission['controller'], $user_permission['action'], $data['role_id'], $id);

            // 현권한과 설정된 권한이 다를경우를 골라내고
            if ($current_permission) {
                if ($user_permission['deny'] == '1') {
                    $change_permissions[] = $user_permission;
                }
            } else {
                if ($user_permission['deny'] == '0') {
                    $change_permissions[] = $user_permission;
                }
            }
        }

        foreach ($change_permissions as $change_permission) {
            $role_permission = $this->Acl->check_role_permission($change_permission['controller'], $change_permission['action'], $data['role_id']);

            // 역할별 권한과 바꾸려는 권한 비교 , 다를경우 admin_permission이 있기 때문임
            if ($role_permission) {
                if ($change_permission['deny'] == '0') {
                    $this->Acl->remove_admin_permission($data['id'], $change_permission['id'], 1);
                } else {
                    $this->Acl->add_admin_permission($data['id'], $change_permission['id'], 1);
                    
                }
            } else {
                if ($change_permission['deny'] == '1') {
                    $this->Acl->remove_admin_permission($data['id'], $change_permission['id'], 0);
                } else {
                    $this->Acl->add_admin_permission($data['id'], $change_permission['id'], 0);
                }
            }
        }
    }

    protected function remove_all_admin_controller_permission($id)
    {
        $admin_permissions = $this->Acl->admin_permissions($id);

        if (empty($admin_permissions['total'])) {
            return true;
        }

        foreach ($admin_permissions['list'] as $admin_permission) {
            $this->Acl->remove_admin_permission($id, $admin_permission['id'], 1);
            $this->Acl->remove_admin_permission($id, $admin_permission['id'], 0);
        }

        return true;
    }

    protected function after_insert_data($id, $data)
    {
        if ($this->return_data['common_data']['branch']['use_admin_ac']) {
            if (!empty($data['card_no'])) {
                $this->load->model('EmployeeAccessCard');
                $this->EmployeeAccessCard->insert(['admin_id' => $id, 'card_no' => $data['card_no']]);
            }

            $this->sync_access_controller($id);
        }

        $eesm_id = $this->check_enable_send_message($id);

        if (!empty($data['enable_send_message'])) {
            if ($eesm_id) {
                $this->EmployeeEnableSendMessage->update(['admin_id' => $id, 'id' => $eesm_id]);
            } else {
                $this->EmployeeEnableSendMessage->insert(['admin_id' => $id]);
            }
        }
    }

    protected function check_enable_send_message($admin_id)
    {
        $this->load->model('EmployeeEnableSendMessage');
        $this->EmployeeEnableSendMessage->admin_id = $admin_id;
        $employee_enable_send_messages = $this->EmployeeEnableSendMessage->get_index();

        if (empty($employee_enable_send_messages['total'])) {
            return false;
        }

        $eesm_id = false;

        foreach ($employee_enable_send_messages['list'] as $employee_enable_send_message) {
            if ($employee_enable_send_message['admin_id'] == $admin_id) {
                $eesm_id = $employee_enable_send_message['id'];
            }
        }

        return $eesm_id;
    }

    public function delete_confirm($id)
    {
        $this->common_index($id);
        $this->return_data['data']['id'] = $id;
        $this->return_data['data']['fc'] = $this->get_admin_list('fc');
        $this->return_data['data']['trainer'] = $this->get_admin_list('trainer');

        $this->load->model('User');

        if ($this->return_data['data']['content']['is_fc'] or $this->return_data['data']['content']['is_trainer']) {
            if ($this->return_data['data']['content']['is_trainer']) {
                $this->User->trainer_id = $this->return_data['data']['content']['id'];
                $this->return_data['data']['trainer_user']['total'] = $this->User->get_count();
            }

            if ($this->return_data['data']['content']['is_fc']) {
                if ($this->return_data['data']['content']['is_trainer']) {
                    unset($this->User->trainer_id);
                }

                $this->User->fc_id = $this->return_data['data']['content']['id'];
                $this->return_data['data']['fc_user']['total'] = $this->User->get_count();
            }
        }

        $this->layout->render('employees/delete', $this->return_data);
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
                echo json_encode(['result' => 'error', 'message' => validation_errors()]);
            }
        } else {
            $data = $this->set_delete_data($content, $this->input->post(null, true));

            if ($this->{$this->model}->delete($id)) {
                $this->after_delete_data($content, $data);

                if ($this->format == 'json') {
                    echo json_encode(['result' => 'success', 'message' => $this->delete_complete_message($content), 'redirect_path' => $this->delete_redirect_path($content)]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'success', 'message' => $this->delete_complete_message($content)]);
                    redirect($this->delete_redirect_path($content));
                }
            } else {
                if ($this->format == 'json') {
                    echo json_encode(['result' => 'error', 'message' => _('Delete Fail')]);
                } else {
                    $this->session->set_flashdata('message', ['type' => 'danger', 'message' => _('Delete Fail')]);
                    redirect($this->delete_redirect_path($content));
                }
            }
        }

        if ($this->return_data['common_data']['branch']['use_admin_ac']) {
            $this->sync_access_controller($id, 'delete');
        }
    }

    public function set_delete_form_validation()
    {
        $this->form_validation->set_rules('id', _('id'), 'integer');
        $this->form_validation->set_rules('after_fc_id', _('after_fc_id'), 'integer');
        $this->form_validation->set_rules('after_trainer_id', _('after_trainer_id'), 'integer');
    }

    public function check_user_id()
    {
        $users = $this->input->post('user_id');

        if (!is_array($users)) {
            $this->form_validation->set_message('check_user_id', _('The %s field is required.'));

            return false;
        }

        foreach ($users as $user) {
            if (!filter_var($user, FILTER_VALIDATE_INT)) {
                return false;
            }
        }

        return true;
    }

    public function check_unselect_user_id()
    {
        $unselect_users = $this->input->post('unselect_user_id');

        foreach ($unselect_users as $unselect_user) {
            if (!filter_var($unselect_user, FILTER_VALIDATE_INT)) {
                return false;
            }
        }

        return true;
    }    

    public function user_search_oc($type = null)
    {
        if(empty($type)) {
            $this->session->unset_userdata('employee_search_open');
        } else {
            $this->session->set_userdata('employee_search_open',true);
        }

        echo json_encode(['result' => 'success']);
    }    

    public function index_oc($type = null)
    {
        if (in_array($type, ['default', 'permission', 'access-control'])) {
            $this->session->set_userdata('employee_open', $type);
        } else {
            $this->session->unset_userdata('employee_open');
        }

        echo json_encode(['result' => 'success']);
    }

    protected function after_delete_data(array $content, $data = null)
    {
        if (!empty($data['after_trainer_id']) or !empty($data['after_fc_id'])) {
            if (!empty($data['after_trainer_id'])) {
                $this->load->model('UserTrainer');
                $this->UserTrainer->change_trainer($data['after_trainer_id'], $content['id']);

                $this->load->model('EnrollTrainer');
                $this->EnrollTrainer->change_trainer($data['after_trainer_id'], $content['id']);
            }

            if (!empty($data['after_fc_id'])) {
                $this->load->model('UserFc');
                $this->UserFc->change_fc($data['after_fc_id'], $content['id']);

                $this->load->model('CounselManager');
                $this->CounselManager->change_fc($data['after_fc_id'], $content['id']);
            }
        }


        $this->load->model('EmployeeAccessCard');
        $this->EmployeeAccessCard->delete_by_parent_id($content['id']);

        $this->sync_access_controller($content['id']);
    }
}
