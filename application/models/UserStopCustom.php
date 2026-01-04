<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class UserStopCustom extends SL_SubModel
{
    protected $table = 'user_stop_customs';
    protected $parent_id_name = 'order_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('order_id', 'custom_days', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if (!empty($this->order_id)) {
            $this->pdo->where(array('order_id' => $this->order_id));
        }
        
        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table, $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (!empty($this->order_id)) {
            $this->pdo->where(array('order_id' => $this->order_id));
        }

        if (isset($id)) {
            $this->pdo->where(array($this->table . '.id' => $id));
        }

        return $this->pdo->count_all_results($this->table);
    }
}
