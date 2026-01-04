<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class No_fcs extends SL_Controller
{
    protected $model = 'NoFc';
    protected $permission_controller = 'employees';
    protected $script = 'no-fcs/index.js';

    protected function index_data($category_id = null)
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
            } else {

                $this->return_data['data']['fc'] = $this->get_admin_list('fc', 1000, 0, false);

                if($this->input->get_post('real_all')) {
                    $this->return_data['data']['real_all'] = true;
                }
            }

            $list=$this->get_search_list();

            $this->return_data['data']['total'] = $list['total'];

            if ($list['total']) {
                $this->return_data['data']['list'] = $list['list'];
            }

            $this->setting_pagination(['total_rows' => $list['total']]);
            $this->return_data['data']['per_page'] = $this->per_page;
            $this->return_data['data']['page'] = $this->page;
        } else {
            $users=array();
            
            if($this->input->post('real_all')) {
                $this->per_page=100000;
                $list=$this->get_search_list(true);
                
                foreach($list['list'] as $value) {
                    $users[]=$value['id'];
                }
                

            } else {
                if($this->input->post('select_oe')) {
                    $users=$this->get_oe_list($this->input->post('select_oe'));
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

            $this->insert_fc($users);
            $this->session->set_flashdata('message', ['type' => 'success', 'message' => _('Sucessfully Change User`s Manager')]);
            $redirect_url='/no-fcs';

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

    protected function set_add_form_data()
    {
        $this->index_data();
    }

    protected function set_edit_form_data(array $content)
    {
        $this->index_data();
        $this->return_data['data']['content'] = $content;
    }

    protected function set_form_validation($id = null)
    {
        $this->form_validation->set_rules('title', _('Title'), 'required|min_length[3]|max_length[60]');
        $this->form_validation->set_rules('content', _('Content'), 'required|trim');
    }

    private function get_search_list($id_only=false)
    {
        $this->load->model('NoFc');


            if(!empty($id_only)) {
                $this->NoFc->id_only=true;
            }

            $list = $this->NoFc->get_index($this->per_page, $this->page);

        
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

    private function get_oe_list ($oe='odd') {
        $users=array();
        $tt=array();
        $t_users=array();    

        $this->per_page=100000;
        $list=$this->get_search_list();
                
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

    private function insert_fc(Array $users)
    {
        $fc_id = $this->input->post('change_fc_id');


        $this->load->model('NoFc');
        foreach ($users as $user) {
            $this->NoFc->insert_fc($fc_id, $user);
        }
    }
}
