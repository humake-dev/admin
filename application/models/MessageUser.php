<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class MessageUser extends SL_SubModel
{
    protected $table = 'message_users';
    protected $parent_id_name = 'message_id';
    protected $accepted_attributes = array('message_id', 'user_id');

    public function get_count($id = null)
    {
        $this->pdo->join('users AS u', 'mu.user_id=u.id');

        if (isset($id)) {
            $this->pdo->where(array('mu.' . $this->p_id => $id));
            return $this->pdo->count_all_results($this->table . ' as mu');
        }

        if (!empty($this->parent_id)) {
            $this->pdo->where(array('mu.' . $this->parent_id_name => $this->parent_id));
        }

        return $this->pdo->count_all_results($this->table . ' as mu');
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('mu.*,u.name');
        $this->pdo->join('users AS u', 'mu.user_id=u.id');

        if (!empty($this->parent_id)) {
            $this->pdo->where(array('mu.' . $this->parent_id_name => $this->parent_id));
        }

        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as mu', $per_page, $page);

        return $query->result_array();
    }

    // 메세지 기록은 실제로 지우지 않도록 한다.
    public function delete($id)
    {
        return $this->pdo->update($this->table, array('enable' => 0), array('id' => $id));
    }    
}
