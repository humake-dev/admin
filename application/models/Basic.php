<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Basic extends SL_Model
{
    protected $accepted_attributes = array('branch_id', 'title', 'share', 'enable', 'updated_at', 'created_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $query = $this->pdo->get($this->table . ' as uc', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {

        return $this->pdo->count_all_results($this->table . ' as uc');
    }
}
