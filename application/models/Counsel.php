<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Counsel extends SL_Model
{
    protected $table = 'counsels';
    protected $table_content = 'counsel_contents';
    protected $accepted_attributes = ['branch_id', 'title', 'execute_date', 'type', 'question_course', 'complete', 'updated_at', 'created_at'];
    protected $search_type = 'default';
    public $search;

    protected function set_search($count = false)
    {
        if (isset($this->search)) {
            switch ($this->search_type) {
                case 'field':
                    if (!empty($this->search['search_field']) and !empty($this->search['search_word'])) {
                        $this->pdo->like('tu.' . $this->search['search_field'], trim($this->search['search_word']));
                    }
                    break;
                default:
                    if (isset($this->search['complete'])) {
                        if (in_array($this->search['complete'], ['0', '1'])) {
                            $this->pdo->where(['c.complete' => $this->search['complete']]);
                        }
                    }

                    if (!empty($this->search['type'])) {
                        $this->pdo->where(['c.type' => $this->search['type']]);
                    }

                    if (!empty($this->search['question_course'])) {
                        $this->pdo->where(['c.question_course' => $this->search['question_course']]);
                    }

                    if (!empty($this->search['status'])) {
                        $this->pdo->where(['c.status' => $this->search['status']]);
                    }

                    if (!empty($this->search['manager'])) {
                        $this->pdo->where(['m.id' => $this->search['manager']]);
                    }

                    if (!empty($this->start_date)) {
                        $this->pdo->where('c.execute_date >=', $this->start_date);
                    }

                    if (!empty($this->end_date)) {
                        $this->pdo->where('c.execute_date <=', $this->end_date);
                    }

                    if (!empty($this->search['no_manager'])) {
                        $this->pdo->where('cm.id is NULL');
                    }
            }
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(['cu.user_id' => $this->user_id]);
        }

        if (!empty($this->temp_user_id)) {
            $this->pdo->where(['tu.id' => $this->temp_user_id]);
        }

        if(!empty($this->phone)) {
            $this->pdo->where('tu.phone is not null AND length(tu.phone)>6');
        }

        $this->pdo->where(['c.branch_id' => $this->session->userdata('branch_id'), 'c.enable' => 1]);
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if(empty($this->id_only)) {
            $select = 'c.*,if(u.id,u.name,tu.name) as user_name,if(u.id,u.name,tu.name) as name,uac.card_no,u.gender,if(u.id,u.phone,tu.phone) as phone,a.name as counselor_name,cu.user_id,tu.id as temp_user_id,cm.admin_id as manager_id,m.name as manager_name,cm.id as cm_id';
        } else {
            $select = 'u.id';
        }

        $this->pdo->select($select);
        $this->pdo->join('counsel_users AS cu', 'cu.counsel_id = c.id', 'left');
        $this->pdo->join('users AS u', 'cu.user_id = u.id', 'left');
        $this->pdo->join('user_access_cards AS uac', 'uac.user_id = u.id', 'left');
        $this->pdo->join('temp_users AS tu', 'tu.counsel_id = c.id', 'left');
        $this->pdo->join('counsel_admins AS ca', 'ca.counsel_id = c.id', 'left');
        $this->pdo->join('admins AS a', 'ca.admin_id = a.id', 'left');
        $this->pdo->join('counsel_managers AS cm', 'cm.counsel_id = c.id', 'left');
        $this->pdo->join('admins AS m', 'cm.admin_id = m.id', 'left');

        $this->set_search();

        $this->pdo->where(array('c.enable'=>1));
        $this->pdo->where_not_in('c.type', array('D'));
        $this->pdo->order_by('c.id', 'desc');
        $query = $this->pdo->get($this->table . ' as c', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('counsel_users AS cu', 'cu.counsel_id = c.id', 'left');
        $this->pdo->join('users AS u', 'cu.user_id = u.id', 'left');
        $this->pdo->join('temp_users AS tu', 'tu.counsel_id = c.id', 'left');
        $this->pdo->join('counsel_managers AS cm', 'cm.counsel_id = c.id', 'left');
        $this->pdo->join('admins AS m', 'cm.admin_id = m.id', 'left');

        if (isset($id)) {
            $this->pdo->where(['c.id' => $id]);

            return $this->pdo->count_all_results($this->table . ' as c');
        }

        $this->set_search(true);
        $this->pdo->where(array('c.enable'=>1));
        $this->pdo->where_not_in('c.type', array('D'));

        return $this->pdo->count_all_results($this->table . ' as c');
    }

    protected function get_content_data($id)
    {
        $select='c.*,if(u.id,u.name,tu.name) as user_name,if(u.id,u.phone,tu.phone) as phone,a.name as counselor,cu.user_id,tu.id as temp_user_id,cc.content,ca.admin_id as counselor_id,m.name as manager,cm.admin_id as manager_id';

        $this->pdo->join('counsel_contents AS cc', 'cc.id = c.id', 'left');
        $this->pdo->join('counsel_users AS cu', 'cu.counsel_id = c.id', 'left');
        $this->pdo->join('users AS u', 'cu.user_id = u.id', 'left');
        $this->pdo->join('temp_users AS tu', 'tu.counsel_id = c.id', 'left');
        $this->pdo->join('counsel_admins AS ca', 'ca.counsel_id = c.id', 'left');
        $this->pdo->join('counsel_managers AS cm', 'cm.counsel_id = c.id', 'left');
        $this->pdo->join('admins AS a', 'ca.admin_id = a.id', 'left');
        $this->pdo->join('admins AS m', 'cm.admin_id = m.id', 'left');

        if($this->session->userdata('center_id')) {
            $select.=',b.title as branch_name';
            $this->pdo->join('branches AS b', 'c.branch_id = b.id');
        }

        $this->pdo->select($select);

        $this->pdo->where(['c.id' => $id]);
        $query = $this->pdo->get($this->table . ' as c');

        return $query->row_array();
    }

    // 상담 기록은 실제로는 지우지 않도록 한다.
    public function delete($id)
    {
        return $this->pdo->update($this->table, ['enable' => 0, 'updated_at' => $this->now], ['id' => $id]);
    }
}
