<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class EmployeeEnableSendMessage extends SL_Model
{
    protected $table = 'admin_enable_send_messages';
    protected $accepted_attributes = array('admin_id', 'enable', 'created_at', 'updated_at');

    public function get_count($id = null)
    {
        $this->pdo->join('admins as a', 'aesm.admin_id=a.id');

        if (isset($id)) {
            $this->pdo->where(array('aesm.id' => $id));
            return $this->pdo->count_all_results($this->table . ' as aesm');
        }

        if (!empty($this->admin_id)) {
            $this->pdo->where(array('a.id' => $this->admin_id));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1, 'aesm.enable' => 1));

        return $this->pdo->count_all_results($this->table . ' as aesm');
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('aesm.*,a.name,a.phone');
        $this->pdo->join('admins as a', 'aesm.admin_id=a.id');

        if (!empty($this->admin_id)) {
            $this->pdo->where(array('a.id' => $this->admin_id));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1, 'aesm.enable' => 1));

        $this->pdo->order_by('aesm.' . $order, $desc);
        $query = $this->pdo->get($this->table . ' as aesm', $per_page, $page);
        return $query->result_array();
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('aesm.*,a.name,a.phone');
        $this->pdo->join('admins as a', 'aesm.admin_id=a.id');
        $this->pdo->where(array('aesm.id' => $id));
        $query = $this->pdo->get($this->table . ' as aesm');
        return $query->row_array();
    }
}
