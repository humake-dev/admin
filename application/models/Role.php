<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Role extends SL_Model
{
    protected $table = 'roles';
    protected $accepted_attributes = array('title', 'description', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as c', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('c.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as c');
    }

    public function get_show_list($per_page = 1000, $page = 0)
    {
        $result = array();
        $this->pdo->where(array('r.show_list' => 1));
        $this->pdo->where('r.id>=' . $this->session->userdata('role_id'));
        $result['total'] = $this->pdo->count_all_results($this->table . ' as r');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->where(array('r.show_list' => 1));
        $this->pdo->where('r.id>=' . $this->session->userdata('role_id'));
        $this->pdo->order_by('r.id', 'desc');
        $query = $this->pdo->get($this->table . ' as r', $per_page, $page);
        $result['list'] = $query->result_array();
        return $result;
    }
}
