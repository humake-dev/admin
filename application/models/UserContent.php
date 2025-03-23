<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class UserContent extends SL_Model
{
    protected $table = 'user_contents';
    protected $accepted_attributes = array('user_id', 'content', 'enable', 'updated_at', 'created_at');
    protected $user_id = false;

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('uc.*,u.name as user_name');
        $this->pdo->join('users as u', 'uc.user_id=u.id');
        $this->pdo->where(array('uc.user_id' => $this->user_id, 'uc.enable' => 1));
        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as uc', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('users as u', 'uc.user_id=u.id');
        if (isset($id)) {
            $this->pdo->where(array('uc.id' => $id));
        } else {
            $this->pdo->where(array('uc.user_id' => $this->user_id, 'uc.enable' => 1));
        }

        return $this->pdo->count_all_results($this->table . ' as uc');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('uc.*');
        $this->pdo->join('users as u', 'uc.user_id = u.id');
        $this->pdo->where(array('uc.id' => $id));
        $query = $this->pdo->get($this->table . ' as uc');

        return $query->row_array();
    }
}
