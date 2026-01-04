<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class EntranceNotUser extends SL_Model
{
    protected $table = 'entrance_not_users';
    protected $accepted_attributes = array('user_id', 'created_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if (!empty($this->user_id)) {
            $this->pdo->where(array('user_id' => $this->user_id));
        }
        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table, $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (!empty($this->user_id)) {
            $this->pdo->where(array('user_id' => $this->user_id));
        }

        if (isset($id)) {
            $this->pdo->where(array($this->table . '.id' => $id));
        }

        return $this->pdo->count_all_results($this->table);
    }
}
