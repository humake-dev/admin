<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';
require_once 'Ac_sync.php';
require_once 'Validate_person.php';
require_once 'Pagination_aside.php';

class Users extends SL_Controller
{
    use Ac_sync;
    use Validate_person;
    use Pagination_aside;
    protected $model = 'User';
    protected $use_index_content = true;
    protected $script = 'users/add.js';
    
    /*public function index($page = 0)
    {
        // Home Controller
    }

    public function view($id = null)
    {
        if (empty($id)) {
            show_404();
        }

        // Home Controller
    } */

    protected function set_add_form_data($id = null)
    {
        $this->return_data['data']['fc'] = $this->get_admin_list('fc');
        $this->return_data['data']['trainer'] = $this->get_admin_list('trainer');

        $this->load->model('Job');
        $this->return_data['data']['job'] = $this->Job->get_index(100, 0);

        if ($this->input->get_post('temp_user_id')) {
            $this->load->model('TempUser');
            $content = $this->TempUser->get_content($this->input->get_post('temp_user_id'));

            $this->return_data['data']['content'] = $content;
            $this->return_data['data']['temp_user_id'] = $content['id'];
        }
    }

    protected function set_edit_form_data(array $content)
    {
        // Insert 시의 데이터 처리와 동일
        $this->set_add_form_data();

        // 그 외 추가 항목들
        $this->set_page();
        
        $this->get_user_list($this->per_page, $this->page);

        $this->setting_pagination(array('base_url' => base_url().$this->router->fetch_class().'/'.$this->router->fetch_method().'/'.$content['id'], 'total_rows' => $this->return_data['data']['user']['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->return_data['data']['content'] = $content;
        $this->return_data['data']['user_content'] = $content;

        $this->layout->add_js('home/index.js');
    }

    protected function set_insert_data($data)
    {
        $data['name'] = trim($data['name']);
        // 전화번호 - 는 제거

        if (empty($data['phone'])) {
            $data['phone'] = null;
        } else {
            $data['phone'] = $this->create_valid_phone($data['phone']);
        }

        if ($this->return_data['common_data']['branch']['use_access_card']) {
            if (empty($data['card_no'])) { // 카드번호가 없으면 만들어 넣는다
                $data['card_no'] = $this->create_card_no($data['phone'], true);
            }
        }

        // 개명시 처리도 제작 필요 = 현재 없음
        if ($this->router->fetch_method() == 'add') {
            // 동명이인 번호 붙이기
            $same_name_count = $this->{$this->model}->get_count_by_name($data['name']);

            if ($same_name_count) {
                foreach (range(1, 100) as $value) {
                    if (!$this->{$this->model}->get_count_by_name($data['name'].'-'.$value)) {
                        $data['name'] = $data['name'].'-'.$value;
                        break;
                    }
                }
            }
        }

        if (empty($data['registration_date'])) {
            $data['registration_date'] = $this->today;
        }

        if (empty($data['job_id'])) {
            $data['job_id'] = null;
        }

        if (empty($data['visit_route'])) {
            $data['visit_route'] = null;           
        }

        if (empty($data['company'])) {
            $data['company'] = null;
        }

        // 말안되는 데이터들 제거
        if (empty($data['birthday'])) {
            $data['birthday'] = null;
        } else {
            $b = new DateTime($data['birthday'], $this->timezone);
            if ($b->format('Y') < 1900 or $b->format('Y') > 2100) {
                $data['birthday'] = null;
            }
        }

        return $data;
    }

    protected function set_update_data($id, $data)
    {
        $data = $this->set_insert_data($data);
        $data['id'] = $id;

        if ($data['birthday'] == '0000-00-00') {
            $data['birthday'] = null;
        }

        return $data;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('phone', _('Phone'), 'min_length[4]|max_length[15]|callback_numeric_dash');
        $this->form_validation->set_rules('gender', _('Gender'), 'in_list[1,0]');
        $this->form_validation->set_rules('registration_date', _('Registration Date'), 'callback_valid_date');
        $this->form_validation->set_rules('fc_id', _('User FC'), 'integer');
        $this->form_validation->set_rules('trainer_id', _('User Trainer'), 'integer');
        $this->form_validation->set_rules('job_id', _('Job'), 'integer');
        $this->form_validation->set_rules('visit_route', _('Visit Route'), 'trim');
        $this->form_validation->set_rules('company', _('Company'), 'trim');
        $this->form_validation->set_rules('temp_user_id', _('Temp User'), 'integer');

        if ($this->input->post('birthday')) {
            $this->form_validation->set_rules('birthday', _('Birthday'), 'callback_valid_date');
        }

        $this->form_validation->set_rules('name', _('Name'), 'required|trim|min_length[2]|max_length[60]');

        if ($this->return_data['common_data']['branch']['use_access_card']) {
            $this->form_validation->set_rules('card_no', _('Access Card No'), 'trim|numeric|min_length[10]|max_length[10]|callback_unique_card_no['.$id.']');
        }
    }

    protected function insert_complete_message($id)
    {
        return _('Successfully Created User');
    }

    protected function add_redirect_path($id)
    {
        if ($this->input->post('return_url')) {
            $return_url = $this->input->post('return_url');
        } else {
            $return_url = '/view/'.$id;
        }

        return $return_url;
    }

    protected function after_insert_data($id, $data)
    {
        $data['user_id'] = $id;

        if (empty($data['edit_content'])) {     
            if (!empty($data['visit_route']) or !empty($data['company']) or !empty($data['job_id'])) {
                $this->load->model('UserAdditional');
                $this->UserAdditional->insert($data);
            }
        } else {
            $this->load->model('UserAdditional');
            $ua_count = $this->UserAdditional->get_count_by_parent_id($id);

            if($ua_count) {
                if (!empty($data['visit_route']) or !empty($data['company']) or !empty($data['job_id'])) {
                    $this->UserAdditional->update(array_merge($data,array('id'=>$data['edit_content']['ua_id'])));
                } else {
                    $this->UserAdditional->delete($data['edit_content']['ua_id']);
                }
            } else {
                $this->UserAdditional->insert($data);  
            }           
        }

        if (empty($data['trainer_id'])) {
            if (!empty($data['edit_content'])) {
                $this->load->model('UserTrainer');
                $trainer_count = $this->UserTrainer->get_count_by_parent_id($id);

                if (!empty($trainer_count)) {
                    $this->UserTrainer->delete($data['edit_content']['ut_id']);
                }
            }
        } else {
            $this->load->model('UserTrainer');
            $trainer_count = $this->UserTrainer->get_count_by_parent_id($id);

            if (empty($trainer_count)) {
                $this->UserTrainer->insert(array('trainer_id' => $data['trainer_id'], 'user_id' => $id));
            } else {
                if ($data['edit_content']['trainer_id'] != $data['trainer_id']) {
                    $this->UserTrainer->update(array('trainer_id' => $data['trainer_id'], 'id' => $data['edit_content']['ut_id']));
                }
            }
        }

        if (empty($data['fc_id'])) {
            if (!empty($data['edit_content'])) {
                $this->load->model('UserFc');
                $fc_count = $this->UserFc->get_count_by_parent_id($id);

                if (!empty($fc_count)) {
                    $this->UserFc->delete($data['edit_content']['ufc_id']);
                }
            }
        } else {
            $this->load->model('UserFc');
            $fc_count = $this->UserFc->get_count_by_parent_id($id);

            if (empty($fc_count)) {
                $this->UserFc->insert(array('fc_id' => $data['fc_id'], 'user_id' => $id));
            } else {
                if ($data['edit_content']['fc_id'] != $data['fc_id']) {
                    $this->UserFc->update(array('fc_id' => $data['fc_id'], 'id' => $data['edit_content']['ufc_id']));
                }
            }
        }

        if ($this->return_data['common_data']['branch']['use_access_card']) {
            $this->load->model('UserAccessCard');
            $uac_count = $this->UserAccessCard->get_count_by_parent_id($id);

            if (empty($uac_count)) {
                if (!empty($data['card_no'])) {
                    $this->UserAccessCard->insert(array('user_id' => $id, 'card_no' => $data['card_no']));
                }
            } else {
                if (empty($data['card_no'])) {
                    $this->UserAccessCard->delete_by_parent_id($id);
                } else {
                    $uac_content = $this->UserAccessCard->get_content_by_parent_id($id);
                    $this->UserAccessCard->update(array('id' => $uac_content['id'], 'card_no' => $data['card_no']));
                }
            }
        }

        if (!empty($data['temp_user_id'])) {
            $this->transfer_temp_user($id, $data['temp_user_id']);
        }

        return $this->sync_access_controller($id);
    }

    protected function transfer_temp_user($id, $temp_user_id)
    {
        $this->load->model('UserTempUserTransfer');
        $this->UserTempUserTransfer->insert(array('user_id' => $id, 'temp_user_id' => $temp_user_id));

        $this->load->model('TempUserContent');
        $this->TempUserContent->temp_user_id = $temp_user_id;
        $temp_content_list = $this->TempUserContent->get_index(1, 0);
        if (!empty($temp_content_list['total'])) {
            $temp_content = $temp_content_list['list'][0];

            $this->load->model('UserContent');
            $this->UserContent->insert(array('user_id' => $id, 'content' => $temp_content['content']));
        }
        $this->load->model('TempUser');
        $this->TempUser->update(array('id' => $temp_user_id, 'enable' => 0));

        $this->load->model('Counsel');
        $this->Counsel->temp_user_id = $temp_user_id;
        $counsel_list = $this->Counsel->get_index(100, 0);

        if ($counsel_list['total']) {
            $this->load->model('CounselUser');

            foreach ($counsel_list['list'] as $counsel) {
                $this->CounselUser->insert(array('user_id' => $id, 'counsel_id' => $counsel['id']));
            }
        }

        $this->load->model('Message');
        $this->Message->temp_user_id = $temp_user_id;
        $message_list = $this->Message->get_index(100, 0);

        if ($message_list['total']) {
            $this->load->model('MessageUser');

            foreach ($message_list['list'] as $message) {
                $this->MessageUser->insert(array('user_id' => $id, 'message_id' => $message['id']));
            }
        }
    }

    protected function after_update_data($id, $data)
    {
        return $this->after_insert_data($id, $data);
    }

    public function select($type = 'multi')
    {
        $this->set_page();

        if ($this->input->get('all')) {
            $per_page = '1000000';
            $page = 0;
        } else {
            $per_page = $this->per_page;
            $page = $this->page;
        }

        $this->get_user_list($per_page, $page);
        if ($this->format == 'json') {
            if ($this->return_data['data']['user']['total']) {
                $result = array('result' => 'success');
                $result['total'] = $this->return_data['data']['user']['total'];
                $result['list'] = $this->return_data['data']['user']['list'];

                if ($this->input->get('rent_info')) {
                    if ($result['total'] == 1) {
                        $this->load->model('Rent');
                        $this->Rent->user_id = $result['list'][0]['id'];
                        $this->Rent->get_current_only = 1;
                        $result['rent_list'] = $this->Rent->get_index(100, 0);
                        $result['rent_info'] = 1;
                    }
                } else {
                    $result['rent_list']['total'] = 0;
                    $result['rent_info'] = 0;
                }

                if ($this->input->get('enroll_info')) {
                    if ($result['total'] == 1) {
                        $this->load->model('Enroll');
                        $this->Enroll->user_id = $result['list'][0]['id'];
                        $this->Enroll->get_current_only = 1;
                        $result['enroll_list'] = $this->Enroll->get_index(100, 0);
                        $result['enroll_info'] = 1;
                    }
                } else {
                    $result['enroll_list']['total'] = 0;
                    $result['enroll_info'] = 0;
                }

                foreach ($result['list'] as $index => $user_content) {
                    if (!empty($user_content['picture_url'])) {
                        $result['list'][$index]['picture_url'] = getPhotoPath('user', $user_content['branch_id'], $user_content['picture_url'], 'large');
                    }
                }

                if ($result['total'] == 1) {
                    $user_content = $result['list'][0];
                    $result['content'] = $user_content;
                }

                echo json_encode($result);
            } else {
                echo json_encode(array('result' => 'success', 'total' => $this->return_data['data']['user']['total']));
            }
        } else {
            if ($type == 'single') {
                $this->return_data['data']['type'] = 'single';
            } else {
                $this->return_data['data']['type'] = 'multi';
            }

            $this->setting_pagination(array('total_rows' => $this->return_data['data']['user']['total']));
            $this->return_data['data']['per_page'] = $this->per_page;
            $this->return_data['data']['page'] = $this->page;

            $this->script = 'users/select.js';
            $this->render_format();
        }
    }

    public function delete_confirm($id)
    {
        $this->set_page();

        $content = $this->get_view_content_data($id);

        $list = $this->get_user_list($this->per_page, $this->page);

        $this->return_data['data']['id'] = $id;

        $this->setting_pagination(array('base_url' => base_url().$this->router->fetch_class().'/'.$this->router->fetch_method().'/'.$id, 'total_rows' => $this->return_data['data']['user']['total']));
        $this->return_data['data']['per_page'] = $this->per_page;
        $this->return_data['data']['page'] = $this->page;

        $this->return_data['data']['content'] = $content;
        $this->return_data['data']['user_content'] = $content;

        $this->layout->render('/users/delete', $this->return_data);
    }

    public function ac_sync($id)
    {
        if ($this->sync_access_controller($id)) {
            $this->session->set_flashdata('message', array('type' => 'success', 'message' => _('Successfully Ac Sync')));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata('message', array('type' => 'danger', 'message' => _('Sync Fail')));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    protected function after_delete_data(array $content, $data = null)
    {
        $this->sync_access_controller($content['id']);
    }

    protected function delete_redirect_path(array $content)
    {
        return '/';
    }

    protected function get_error_messages()
    {
        $message = parent::get_error_messages();
        $message['numeric_dash'] = _('The %s field allow only number and dash');

        return $message;
    }
}
