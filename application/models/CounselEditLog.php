<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class CounselEditLog extends SL_Model
{
    protected $table = 'counsel_edit_logs';
    protected $accepted_attributes = array('counsel_id', 'admin_id', 'revision', 'content', 'enable', 'created_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('cel.*,a.name as editor,if(cu.user_id,u.name,tu.name) as user_name,if(cu.user_id,u.name,tu.name) as name,c.title,cu.user_id,tu.id as temp_user_id,(select count(*) FROM counsel_edit_log_fields WHERE counsel_edit_log_id=cel.id) as field_change_count');
        $this->pdo->join('counsels as c', 'cel.counsel_id=c.id');
        $this->pdo->join('counsel_users as cu', 'cu.counsel_id=c.id','left');
        $this->pdo->join('users AS u', 'cu.user_id = u.id', 'left');
        $this->pdo->join('temp_users as tu', 'tu.counsel_id=c.id','left');
        $this->pdo->join('admins as a', 'cel.admin_id=a.id', 'left');

        if (!empty($this->counsel_id)) {
            $this->pdo->where(array('cel.counsel_id' => $this->counsel_id));
        }

        if (!empty($this->start_date)) {
            $this->pdo->where('date(cel.created_at) >=', $this->start_date);
        }

        if (!empty($this->end_date)) {
            $this->pdo->where('date(cel.created_at) <=', $this->end_date);
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('cu.user_id' => $this->user_id));
        }

        if (!empty($this->field)) {
            $this->pdo->join('counsel_edit_log_fields as celf', 'celf.counsel_edit_log_id=cel.id');
            $this->pdo->where(array('celf.field' => $this->field));
        }

        $this->pdo->where(array('c.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->order_by($order, $desc);

        $query = $this->pdo->get($this->table . ' as cel', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('counsels as c', 'cel.counsel_id=c.id');
        $this->pdo->join('counsel_users as cu', 'cu.counsel_id=c.id','left');
        $this->pdo->join('users AS u', 'cu.user_id = u.id', 'left');
        $this->pdo->join('temp_users as tu', 'tu.counsel_id=c.id','left');
        $this->pdo->join('admins as a', 'cel.admin_id=a.id', 'left');

        if (isset($id)) {
            $this->pdo->where(array('cel.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as cel');
        }

        if (!empty($this->counsel_id)) {
            $this->pdo->where(array('cel.counsel_id' => $this->counsel_id));
        }

        if (!empty($this->start_date)) {
            $this->pdo->where('date(cel.created_at) >=', $this->start_date);
        }

        if (!empty($this->end_date)) {
            $this->pdo->where('date(cel.created_at) <=', $this->end_date);
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('cu.user_id' => $this->user_id));
        }

        if (!empty($this->field)) {
            $this->pdo->join('counsel_edit_log_fields as celf', 'celf.counsel_edit_log_id=cel.id');
            $this->pdo->where(array('celf.field' => $this->field));
        }

        $this->pdo->where(array('c.branch_id' => $this->session->userdata('branch_id')));

        return $this->pdo->count_all_results($this->table . ' as cel');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('cel.*,a.name as editor,u.id as user_id,u.name as user_name,c.title,(select count(*) FROM counsel_edit_log_fields WHERE counsel_edit_log_id=cel.id) as field_change_count');
        $this->pdo->join('counsels as c', 'cel.counsel_id=c.id');
        $this->pdo->join('counsel_users as cu', 'cu.counsel_id=c.id','left');
        $this->pdo->join('users AS u', 'cu.user_id = u.id', 'left');
        $this->pdo->join('temp_users as tu', 'tu.counsel_id=c.id','left');
        $this->pdo->join('admins as a', 'cel.admin_id=a.id', 'left');
        $this->pdo->where(array('c.branch_id' => $this->session->userdata('branch_id'), 'cel.id' => $id));
        $query = $this->pdo->get($this->table . ' as cel');

        return $query->row_array();
    }

    public function get_user_list()
    {
        $result = array();
        $result['total'] = $this->get_user_count();

        if (!$result['total']) {
            return $result;
        }

        $result['list'] = $this->get_user_list_data();

        return $result;
    }

    protected function get_user_count()
    {
        $this->pdo->select('count(*)');
        $this->pdo->join('counsels as c', 'cel.counsel_id=c.id');
        $this->pdo->join('counsel_users as cu', 'cu.counsel_id=c.id');
        $this->pdo->join('users as u', 'cu.user_id=u.id');

        $this->pdo->where(array('c.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->group_by('u.id');

        return $this->pdo->count_all_results($this->table . ' as cel');
    }

    protected function get_user_list_data()
    {
        $this->pdo->select('u.id,u.name');
        $this->pdo->join('counsels as c', 'cel.counsel_id=c.id');
        $this->pdo->join('counsel_users as cu', 'cu.counsel_id=c.id');
        $this->pdo->join('users as u', 'cu.user_id=u.id');

        $this->pdo->where(array('c.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->group_by('u.id');

        $query = $this->pdo->get($this->table . ' as cel');

        return $query->result_array();
    }
}
