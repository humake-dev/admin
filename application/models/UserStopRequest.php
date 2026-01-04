<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class UserStopRequest extends SL_Model
{
    protected $table = 'user_stop_requests';
    protected $search_type = 'default';
    protected $accepted_attributes = array('user_id', 'picture_url','complete', 'enable', 'created_at', 'updated_at');

    protected function set_search($count = false)
    {
        if (isset($this->search)) {
            switch ($this->search_type) {
                case 'field':
                    if (!empty($this->search['search_field']) and !empty($this->search['search_word'])) {
                        $this->pdo->like('u.' . $this->search['search_field'], trim($this->search['search_word']));
                    }
                    break;
                default:
                if (isset($this->search['complete'])) {
                    if (in_array($this->search['complete'], ['0', '1'])) {
                        $this->pdo->where(['usr.complete' => $this->search['complete']]);
                    }
                }

                    if (!empty($this->search['status'])) {
                        $this->pdo->where(['c.status' => $this->search['status']]);
                    }

                    if (!empty($this->search['manager'])) {
                        $this->pdo->where(['a.id' => $this->search['manager']]);
                    }

                    if (!empty($this->start_date)) {
                        $this->pdo->where('usr.stop_start_date >=', $this->start_date);
                    }

                    if (!empty($this->end_date)) {
                        $this->pdo->where('usr.stop_end_date <=', $this->end_date);
                    }

                    if (!empty($this->search['no_manager'])) {
                        $this->pdo->where('a.id is NULL');
                    }
            }
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(['cu.user_id' => $this->user_id]);
        }

        if (isset($id)) {
            $this->pdo->where(array('u.id' => $id));
        } else {
                if(empty($this->branch_id)) {
                    if ($this->session->userdata('center_id')) {
                        $this->pdo->where(array('u.enable' => 1));
                    } else {
                        $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1));
                    }
                } else {
                    $this->pdo->where(array('u.enable' => 1));
                    $this->pdo->where_in('u.branch_id',$this->branch_id);
                }
        }

        if(!empty($this->phone)) {
            $this->pdo->where('u.phone is not null');
        }
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('usr.*,u.name as user_name,b.id as branch_id,b.title as branch_name,u.phone,a.name as manager_name');
        $this->pdo->join('users as u','usr.user_id=u.id');
        $this->pdo->join('branches as b','u.branch_id=b.id');
        $this->pdo->join('user_fcs as uf','uf.user_id=u.id','left');
        $this->pdo->join('admins as a','a.id=uf.fc_id','left');

        $this->set_search();
        
        if(empty($this->session->userdata('center_id'))) {
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
        }

        $this->pdo->where(array('usr.enable' => 1));
        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table.' as usr', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('users as u','usr.user_id=u.id');
        $this->pdo->join('branches as b','u.branch_id=b.id');
        $this->pdo->join('user_fcs as uf','uf.user_id=u.id','left');
        $this->pdo->join('admins as a','a.id=uf.fc_id','left');

        if (isset($id)) {
            $this->pdo->where(array('usr.id' => $id));
        }

        $this->set_search();

        if(empty($this->session->userdata('center_id'))) {
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
        }

        $this->pdo->where(array('usr.enable' => 1));
        return $this->pdo->count_all_results($this->table.' as usr');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('usr.*,u.name as user_name,b.title as branch_name,u.phone');
        $this->pdo->join('users as u','usr.user_id=u.id');
        $this->pdo->join('branches as b','u.branch_id=b.id');

        if(empty($this->session->userdata('center_id'))) {
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
        }

        $this->pdo->where(array('usr.id' => $id,'usr.enable'=>1));
        $query = $this->pdo->get($this->table . ' as usr');

        return $query->row_array();
    }

    public function delete($id)
    {
        return $this->pdo->update($this->table, array('enable' => 0,'updated_at'=>$this->now), array('id' => $id));
    }
}
