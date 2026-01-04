<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Commission extends SL_Model
{
    protected $table = 'account_commissions';
    protected $accepted_attributes = array('admin_id', 'counselor_id', 'user_id', 'execute_date', 'type', 'status', 'phone', 'name', 'updated_at', 'created_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('ac.*,a.transaction_date,a.cash as commission,a.user_id,u.name as user_name,p.title as course_name');
        $this->pdo->join('accounts AS a', 'ac.account_id = a.id');
        $this->pdo->join('users AS u', 'a.user_id = u.id', 'left');
        $this->pdo->join('enrolls AS e', 'ac.enroll_id = e.id', 'left');        
        $this->pdo->join('courses AS c', 'e.course_id = c.id', 'left');
        $this->pdo->join('products AS p', 'c.product_id = p.id', 'left');
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));

        if (!empty($this->search_start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->search_start_date);
        }

        if (!empty($this->search_end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->search_end_date);
        }

        if (isset($this->employee_id)) {
            $this->pdo->where(array('ac.employee_id' => $this->employee_id));
        }

        $this->pdo->order_by('a.transaction_date', 'desc');
        $query = $this->pdo->get($this->table . ' as ac', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('accounts AS a', 'ac.account_id = a.id');
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));

        if (!empty($this->search_start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->search_start_date);
        }

        if (!empty($this->search_end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->search_end_date);
        }

        if (isset($this->employee_id)) {
            $this->pdo->where(array('ac.employee_id' => $this->employee_id));
        }

        return $this->pdo->count_all_results($this->table . ' as ac');
    }
}
