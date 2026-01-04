<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class CounselRequest extends SL_Model
{
    protected $table = 'counsels';
    protected $search_type = 'default';

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
                            if($this->search['complete']) {
                                $this->pdo->where('cr.id is NOT NULL');
                            } else {
                                $this->pdo->where('cr.id is NULL');
                            }
                        }
                    }

                    if (!empty($this->search['question_course'])) {
                        $this->pdo->where(['c.question_course' => $this->search['question_course']]);
                    }

                    if (!empty($this->search['status'])) {
                        $this->pdo->where(['c.status' => $this->search['status']]);
                    }

                    if (!empty($this->search['manager'])) {
                        $this->pdo->where(['a.id' => $this->search['manager']]);
                    }

                    if (!empty($this->start_date)) {
                        $this->pdo->where('c.execute_date >=', $this->start_date);
                    }

                    if (!empty($this->end_date)) {
                        $this->pdo->where('c.execute_date <=', $this->end_date);
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
        $this->pdo->select('c.*,a.name as manager_name,b.title as branch_name,u.name as user_name,u.phone,cr.id as cr_id');
        $this->pdo->join('counsel_users AS cu', 'cu.counsel_id = c.id');
        $this->pdo->join('users AS u', 'cu.user_id = u.id');
        $this->pdo->join('branches AS b', 'c.branch_id = b.id');
        $this->pdo->join('counsel_managers AS cm', 'c.id = cm.counsel_id', 'left');
        $this->pdo->join('admins AS a', 'cm.admin_id = a.id', 'left');
        $this->pdo->join('counsel_responses AS cr', 'cr.counsel_id = c.id', 'left');

        $this->set_search();

        $this->pdo->where(array('c.type' => 'D','c.enable'=>1));

        if(empty($this->session->userdata('center_id'))) {
            $this->pdo->where(array('c.branch_id' => $this->session->userdata('branch_id')));
        }
        
        $this->pdo->order_by('c.id', $desc);
        $query = $this->pdo->get($this->table.' as c', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('counsel_users AS cu', 'cu.counsel_id = c.id');
        $this->pdo->join('users AS u', 'cu.user_id = u.id');
        $this->pdo->join('branches AS b', 'c.branch_id = b.id');
        $this->pdo->join('counsel_managers AS cm', 'c.id = cm.counsel_id', 'left');
        $this->pdo->join('admins AS a', 'cm.admin_id = a.id', 'left');
        $this->pdo->join('counsel_responses AS cr', 'cr.counsel_id = c.id', 'left');

        if (isset($id)) {
            $this->pdo->where(array('c.id' => $id));
        }

        $this->set_search();

        $this->pdo->where(array('c.type' => 'D','c.enable'=>1));

        if(empty($this->session->userdata('center_id'))) {
            $this->pdo->where(array('c.branch_id' => $this->session->userdata('branch_id')));
        }

        return $this->pdo->count_all_results($this->table.' as c');
    }

    protected function get_content_data($id)
    {
        $select='c.*,u.name as user_name,u.phone as phone,a.name as counselor,cu.user_id,cc.content,ca.admin_id as counselor_id,m.name as manager_name,cm.admin_id as manager_id,cr.id as cr_id,cr.content as response';

        $this->pdo->join('counsel_contents AS cc', 'cc.id = c.id', 'left');
        $this->pdo->join('counsel_users AS cu', 'cu.counsel_id = c.id', 'left');
        $this->pdo->join('users AS u', 'cu.user_id = u.id', 'left');
        $this->pdo->join('counsel_admins AS ca', 'ca.counsel_id = c.id', 'left');
        $this->pdo->join('counsel_managers AS cm', 'cm.counsel_id = c.id', 'left');
        $this->pdo->join('admins AS a', 'ca.admin_id = a.id', 'left');
        $this->pdo->join('admins AS m', 'cm.admin_id = m.id', 'left');

        $this->pdo->join('counsel_responses AS cr', 'cr.counsel_id = c.id', 'left');


        if($this->session->userdata('center_id')) {
            $select.=',b.title as branch_name';
            $this->pdo->join('branches AS b', 'c.branch_id = b.id');
        }

        $this->pdo->select($select);

        $this->pdo->where(['c.id' => $id]);
        $query = $this->pdo->get($this->table . ' as c');

        return $query->row_array();
    }

    public function delete($id)
    {
        return $this->pdo->update($this->table, array('enable' => 0,'updated_at'=>$this->now), array('id' => $id));
    }
}
