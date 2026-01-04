<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class UserHeight extends SL_SubModel
{
    protected $table = 'user_heights';
    protected $parent_id_name = 'user_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('user_id', 'height', 'enable', 'created_at', 'updated_at');

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

    public function get_content_data($id)
    {
        $this->pdo->select('bi.*,u.name');
        $this->pdo->join('users AS u', 'bi.user_id=u.id');
        $this->pdo->where(array('bi.id' => $id));
        $query = $this->pdo->get($this->table . ' as bi');

        return $query->row_array();
    }
}
