<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class TempUserContent extends SL_Model
{
    protected $table = 'temp_user_contents';
    protected $accepted_attributes = array('temp_user_id', 'content', 'enable', 'updated_at', 'created_at');
    protected $temp_user_id = false;

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('tuc.*,tu.name');
        $this->pdo->join('temp_users as tu', 'tuc.temp_user_id = tu.id');
        $this->pdo->where(array('tuc.temp_user_id' => $this->temp_user_id, 'tuc.enable' => 1));
        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as tuc', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('temp_users as tu', 'tuc.temp_user_id = tu.id');
        if (isset($id)) {
            $this->pdo->where(array('tuc.id' => $id));
        } else {
            $this->pdo->where(array('tuc.temp_user_id' => $this->temp_user_id, 'tuc.enable' => 1));
        }

        return $this->pdo->count_all_results($this->table . ' as tuc');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('tuc.*,tu.name');
        $this->pdo->join('temp_users as tu', 'tuc.temp_user_id = tu.id');
        $this->pdo->where(array('tuc.id' => $id));
        $query = $this->pdo->get($this->table . ' as tuc');

        return $query->row_array();
    }
}
