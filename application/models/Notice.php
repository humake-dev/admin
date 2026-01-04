<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Notice extends SL_Model
{
    protected $table = 'notices';
    protected $table_content = 'notice_contents';
    protected $accepted_attributes = array('branch_id', 'admin_id', 'title', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('n.*');
        $this->pdo->join('admins as a', 'n.admin_id=a.id');
        $this->pdo->where(array('n.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->order_by($order, $desc);

        $query = $this->pdo->get($this->table . ' as n', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('n.id' => $id));
        }

        $this->pdo->join('admins as a', 'n.admin_id=a.id');
        $this->pdo->where(array('n.branch_id' => $this->session->userdata('branch_id')));

        return $this->pdo->count_all_results($this->table . ' as n');
    }
}
