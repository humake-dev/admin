<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class AdminMoveAvailableBranch extends SL_Model
{
    protected $table = 'admin_move_available_branches';
    protected $accepted_attributes = array('admin_id', 'branch_id');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('b.id,b.title');
        $this->pdo->join('branches as b', 'amab.branch_id=b.id');
        $this->pdo->where(array('amab.admin_id'=>$this->admin_id,'b.enable'=>true));
        $query = $this->pdo->get($this->table . ' as amab', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('branches as b', 'amab.branch_id=b.id');
        $this->pdo->where(array('amab.admin_id'=>$this->admin_id,'b.enable'=>true));
        
        if (isset($id)) {
            $this->pdo->where(array('amab.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as amab');
    }
}


