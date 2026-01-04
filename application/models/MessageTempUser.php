<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class MessageTempUser extends SL_SubModel
{
    protected $table = 'message_temp_users';
    protected $parent_id_name = 'message_id';
    protected $accepted_attributes = array('message_id', 'temp_user_id');

    public function get_count($id = null)
    {
        $this->pdo->join('temp_users AS u', 'tu.temp_user_id=u.id');

        if (isset($id)) {
            $this->pdo->where(array('tu.' . $this->p_id => $id));
            return $this->pdo->count_all_results($this->table . ' as tu');
        }

        if (!empty($this->parent_id)) {
            $this->pdo->where(array('tu.' . $this->parent_id_name => $this->parent_id));
        }

        return $this->pdo->count_all_results($this->table . ' as tu');
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('tu.*,u.name');
        $this->pdo->join('temp_users AS u', 'tu.temp_user_id=u.id');

        if (!empty($this->parent_id)) {
            $this->pdo->where(array('tu.' . $this->parent_id_name => $this->parent_id));
        }

        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as tu', $per_page, $page);

        return $query->result_array();
    }
}
