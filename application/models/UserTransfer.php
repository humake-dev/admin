<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class UserTransfer extends SL_SubModel
{
    protected $table = 'user_transfers';
    protected $parent_id_name = 'user_id';
    protected $parent_unique = true;
    protected $table_id_name = 'user_transfer_id';
    protected $accepted_attributes = array('user_id', 'old_branch_id', 'new_user_id', 'new_branch_id', 'enable', 'created_at', 'updated_at');

    protected function get_content_data($id)
    {
        $this->pdo->select('ut.*,u.name as user_name');
        $this->pdo->join('users as u', 'ut.user_id = u.id');
        $this->pdo->where(array('ut.id' => $id));
        $query = $this->pdo->get($this->table . ' as ut');

        return $query->row_array();
    }

    public function delete($id)
    {
        return $this->pdo->update($this->table, array('enable' => 0, 'updated_at' => $this->now), array('id' => $id));
    }
}
