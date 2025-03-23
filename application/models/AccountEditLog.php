<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class AccountEditLog extends SL_Model
{
    protected $table = 'account_edit_logs';
    protected $accepted_attributes = array('account_id', 'admin_id', 'revision', 'content', 'enable', 'created_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('ael.*,admins.name as editor,u.id as user_id,u.name as user_name,p.title as product_name,(select count(*) FROM account_edit_log_fields WHERE account_edit_log_id=ael.id) as field_change_count');
        $this->pdo->join('accounts as a', 'ael.account_id=a.id');
        $this->pdo->join('users as u', 'a.user_id=u.id');
        $this->pdo->join('admins', 'ael.admin_id=admins.id', 'left');
        $this->pdo->join('account_products as ap', 'ap.account_id=a.id', 'left');
        $this->pdo->join('products as p', 'ap.product_id=p.id', 'left');        

        if (!empty($this->account_id)) {
            $this->pdo->where(array('ael.account_id' => $this->account_id));
        }

        if (!empty($this->start_date)) {
            $this->pdo->where('date(ael.created_at) >=', $this->start_date);
        }

        if (!empty($this->end_date)) {
            $this->pdo->where('date(ael.created_at) <=', $this->end_date);
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('a.user_id' => $this->user_id));
        }

        if (!empty($this->product_id)) {
            $this->pdo->where(array('ap.product_id' => $this->product_id));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->order_by($order, $desc);

        $query = $this->pdo->get($this->table . ' as ael', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('accounts as a', 'ael.account_id=a.id');
        $this->pdo->join('users as u', 'a.user_id=u.id');
        $this->pdo->join('admins', 'ael.admin_id=admins.id', 'left');
        $this->pdo->join('account_products as ap', 'ap.account_id=a.id', 'left');
        $this->pdo->join('products as p', 'ap.product_id=p.id', 'left');  

        if (isset($id)) {
            $this->pdo->where(array('ael.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as ael');
        }

        if (!empty($this->account_id)) {
            $this->pdo->where(array('ael.account_id' => $this->account_id));
        }

        if (!empty($this->start_date)) {
            $this->pdo->where('date(ael.created_at) >=', $this->start_date);
        }

        if (!empty($this->end_date)) {
            $this->pdo->where('date(ael.created_at) <=', $this->end_date);
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('a.user_id' => $this->user_id));
        }

        if (!empty($this->product_id)) {
            $this->pdo->where(array('ap.product_id' => $this->product_id));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));

        return $this->pdo->count_all_results($this->table . ' as ael');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('ael.*,admins.name as editor,u.id as user_id,u.name as user_name,p.title as product_name,(select count(*) FROM account_edit_log_fields WHERE account_edit_log_id=ael.id) as field_change_count');
        $this->pdo->join('accounts as a', 'ael.account_id=a.id');
        $this->pdo->join('users as u', 'a.user_id=u.id');
        $this->pdo->join('admins', 'ael.admin_id=admins.id', 'left');
        $this->pdo->join('account_products as ap', 'ap.account_id=a.id', 'left');
        $this->pdo->join('products as p', 'ap.product_id=p.id', 'left');  

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'ael.id' => $id));
        $query = $this->pdo->get($this->table . ' as ael');

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
        $this->pdo->join('accounts as a', 'ael.account_id=a.id');
        $this->pdo->join('users as u', 'a.user_id=u.id');
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->group_by('u.id');

        return $this->pdo->count_all_results($this->table . ' as ael');
    }

    protected function get_user_list_data()
    {
        $this->pdo->select('u.id,u.name');
        $this->pdo->join('accounts as a', 'ael.account_id=a.id');
        $this->pdo->join('users as u', 'a.user_id=u.id');
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->group_by('u.id');

        $query = $this->pdo->get($this->table . ' as ael');

        return $query->result_array();
    }    
}
