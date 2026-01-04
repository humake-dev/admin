<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class User extends SL_Model
{
    protected $table = 'users';
    protected $accepted_attributes = array('branch_id', 'name', 'phone', 'gender', 'birthday', 'registration_date', 'enable', 'created_at', 'updated_at');
    protected $quantity_only = false;
    protected $search_param;

    protected function set_search($select = false)
    {
        if (isset($this->course_id)) {
            $this->pdo->join('orders as o', 'o.user_id=u.id');
            $this->pdo->join('enrolls as e', 'e.order_id=o.id');
            $this->pdo->where(array('e.course_id' => $this->course_id));

            if ($this->quantity_only) {
                $this->pdo->where('e.quantity>e.use_quantity+(select count(*) FROM reservation_users WHERE enroll_id=e.id and complete in (0,1))');
            } else {
                $this->pdo->where('e.end_date>=curdate()');
            }
        }

        $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id', 'left');
        $this->pdo->join('admins AS fc', 'ufc.fc_id=fc.id', 'left');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');

        if (empty($this->course_id)) {
            $this->pdo->join('user_trainers AS ut', 'ut.user_id=u.id', 'left');
            $this->pdo->join('admins AS trainer', 'ut.trainer_id=trainer.id', 'left');
        } else {
            $this->pdo->join('enroll_trainers as et', 'et.enroll_id=e.id', 'left');
            $this->pdo->join('admins AS trainer', 'et.trainer_id=trainer.id', 'left');
        }

        if (!empty($this->fc_id) or !empty($this->trainer_id)) {
            if (!empty($this->fc_id) and !empty($this->trainer_id)) {
                if (empty($this->course_id)) {
                    $this->pdo->where('(ut.trainer_id='.$this->trainer_id.' AND ufc.fc_id='.$this->fc_id.')', null, false);
                } else {
                    $this->pdo->where('(et.trainer_id='.$this->trainer_id.' AND ufc.fc_id='.$this->fc_id.')', null, false);
                }
            } else {
                if (!empty($this->trainer_id)) {
                    if (empty($this->course_id)) {
                        $this->pdo->where('(ut.trainer_id='.$this->trainer_id.')', null, false);
                    } else {
                        $this->pdo->where('(et.trainer_id='.$this->trainer_id.')', null, false);
                    }
                }

                if (!empty($this->fc_id)) {
                    $this->pdo->where(array('ufc.fc_id' => $this->fc_id));
                }
            }
        }

        if (isset($this->search_param['search_type'])) {
            if($this->search_param['search_type']=='field') {
              if (isset($this->search_param)) {
                  if (!empty($this->search_param['search_field']) and !empty($this->search_param['search_word'])) {

                          if ($this->search_param['search_field'] == 'card_no') {
                              $this->pdo->like('uac.'.$this->search_param['search_field'], $this->search_param['search_word']);
                          } else {
                              $this->pdo->like('u.'.$this->search_param['search_field'], $this->search_param['search_word']);
                          }
                  }
              }
            }
        }        

        if (!empty($this->user_id)) {
            if(is_array($this->user_id)) {
                $this->pdo->where_in('u.id', $this->user_id);
            } else {
                $this->pdo->where(array('u.id'=>$this->user_id));
            }
        }

        if (isset($this->push)) {
            $this->pdo->select('u.*,ud.token');

            if (empty($this->push)) {
                $this->pdo->join('user_devices as ud', 'ud.user_id=u.id', 'left');             
            } else {
                $this->pdo->join('user_devices as ud', 'ud.user_id=u.id');         
            }
        }

        if (!empty($this->phone_only)) {
            $this->pdo->where('u.phone IS NOT NULL AND length(u.phone)>6');
        }

        if (!empty($this->wapos)) {
            $this->pdo->select('u.*,ud.token');
            $this->pdo->join('user_devices as ud', 'ud.user_id=u.id', 'left');          
            $this->pdo->where('(u.phone is NOT NULL and length(u.phone)>6) OR ud.token is NOT NULL');
           
        }
        

        if(isset($this->push) or !empty($this->phone_only) or !empty($this->wapos)) {
            $this->pdo->where('NOT EXISTS(SELECT * FROM user_transfers WHERE user_id=u.id AND old_branch_id=u.branch_id AND enable=1)');
        }

        if (!empty($this->search_start_date)) {
            $this->pdo->where('u.registration_date >="'.$this->search_start_date.'"');
        }

        if (!empty($this->search_end_date)) {
            $this->pdo->where('u.registration_date <="'.$this->search_end_date.'"');
        }

        if (empty($this->branch_id)) {
            if ($this->session->userdata('branch_id')) {
                $this->pdo->join('branches AS b', 'u.branch_id=b.id');
                $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1));
            } else {
                if ($this->session->userdata('center_id')) {
                    if ($select) {
                        $this->pdo->select('u.*');
                    }
                    $this->pdo->join('branches as b', 'b.id=u.branch_id');
                    $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id'), 'u.enable' => 1));
                } else {
                    throw new exception('No Cetner ID And No Branch ID');
                }
            }
        } else {
            $this->pdo->join('branches AS b', 'u.branch_id=b.id');
            $this->pdo->where(array('u.branch_id' => $this->branch_id, 'u.enable' => 1));
        }
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select='DISTINCT u.id,u.*,uac.card_no,ufc.fc_id,fc.name as fc_name,trainer.id as trainer_id,trainer.name as trainer_name,up.id as up_id,up.picture_url,ua.id as ua_id,ua.company,ua.visit_route,j.title as job';
        $this->pdo->select($select, false);
        
        $this->pdo->join('user_pictures AS up', 'up.user_id=u.id', 'left');
        $this->pdo->join('user_additionals AS ua', 'ua.user_id=u.id', 'left');
        $this->pdo->join('jobs AS j', 'ua.job_id=j.id', 'left');
        
        $this->set_search(true);

        $this->pdo->group_by('u.id');
        $this->pdo->order_by('u.'.$order, $desc);
        $query = $this->pdo->get($this->table.' as u', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('u.id' => $id));

            return $this->pdo->count_all_results($this->table.' as u');
        }

        $this->pdo->select('count(distinct u.id)');
        $this->set_search();
        $this->pdo->group_by('u.id');

        return $this->pdo->count_all_results($this->table.' as u');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('u.*,uac.card_no,ut.id as ut_id,ut.trainer_id,trainer.name as trainer_name,ufc.id as ufc_id,ufc.fc_id,fc.name as fc_name,up.id as up_id,up.picture_url,ua.id as ua_id,ua.visit_route,ua.company,j.title as job');
        $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id', 'left');
        $this->pdo->join('admins AS fc', 'ufc.fc_id=fc.id', 'left');
        $this->pdo->join('user_trainers AS ut', 'ut.user_id=u.id', 'left');
        $this->pdo->join('admins AS trainer', 'ut.trainer_id=trainer.id', 'left');
        $this->pdo->join('user_pictures AS up', 'up.user_id=u.id', 'left');
        $this->pdo->join('user_access_cards AS uac', 'uac.user_id=u.id', 'left');
        $this->pdo->join('user_additionals AS ua', 'ua.user_id=u.id', 'left');
        $this->pdo->join('jobs AS j', 'ua.job_id=j.id', 'left');

        if(empty($this->no_branch) and empty($this->session->userdata('center_id'))) {
            if ($this->session->userdata('branch_id')) {
                $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
            }
        }

        $this->pdo->where(array('u.id' => $id));
        $query = $this->pdo->get($this->table.' as u');

        return $query->row_array();
    }

    public function get_count_by_name($name)
    {
        $this->pdo->where(array('branch_id' => $this->session->userdata('branch_id'), 'name' => $name));
        $count = $this->pdo->count_all_results($this->table);

        return $count;
    }

    public function exists_unique_card_no($card_no, $id = null)
    {
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id');

        if (!empty($id)) {
            $this->pdo->where(array('u.id !=' => $id));
        }
        $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id'), 'uac.card_no' => $card_no));

        return $this->pdo->count_all_results($this->table.' as u');
    }

    public function get_content_by_card_no($card_no, $branch_id = null)
    {
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id');

        if (empty($branch_id)) {
            $branch_id = $this->session->userdata('branch_id');
        }

        $this->pdo->where(array('u.branch_id' => $branch_id, 'uac.card_no' => $card_no, 'u.enable' => 1));
        $count = $this->pdo->count_all_results($this->table.' as u');

        if (!$count) {
            return false;
        }

        $this->pdo->select('u.*');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id');
        $this->pdo->where(array('u.branch_id' => $branch_id, 'uac.card_no' => $card_no, 'u.enable' => 1));
        $query = $this->pdo->get($this->table.' as u');
        $result = $query->row_array();

        return $this->get_content($result['id']);
    }

    public function get_content_by_phone($phone)
    {
        $this->pdo->where(array('branch_id' => $this->session->userdata('branch_id'), 'phone' => $phone, 'enable'=>1));
        $count = $this->pdo->count_all_results($this->table);

        if (!$count) {
            return false;
        }

        $this->pdo->where(array('branch_id' => $this->session->userdata('branch_id'), 'phone' => $phone, 'enable'=>1));
        $query = $this->pdo->get($this->table);
        $result = $query->row_array();

        return $result;
    }

    public function get_content_by_old_id($old_user_id, $branch_id = null)
    {
        $this->pdo->join('user_old_ids as uoi', 'uoi.user_id=u.id');

        if (empty($branch_id)) {
            $branch_id = $this->session->userdata('branch_id');
        }

        $this->pdo->where(array('u.branch_id' => $branch_id, 'uoi.old_id' => $old_user_id, 'u.enable' => 1));
        $count = $this->pdo->count_all_results($this->table.' as u');

        if (!$count) {
            return false;
        }

        $this->pdo->select('u.*');
        $this->pdo->join('user_old_ids as uoi', 'uoi.user_id=u.id');
        $this->pdo->where(array('u.branch_id' => $branch_id, 'uoi.old_id' => $old_user_id, 'u.enable' => 1));
        $query = $this->pdo->get($this->table.' as u');
        $result = $query->row_array();

        return $this->get_content($result['id']);
    }
}
