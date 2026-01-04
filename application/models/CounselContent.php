<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class CounselContent extends SL_Model
{
    protected $table = 'counsel_contents';
    protected $accepted_attributes = array('counsel_id', 'content', 'enable', 'updated_at', 'created_at');

    protected function get_content_data($id)
    {
        $this->pdo->select('cc.*');
        $this->pdo->join('counsels as c', 'cc.id = c.id');
        $this->pdo->where(array('cc.id' => $id));
        $query = $this->pdo->get($this->table . ' as cc');

        return $query->row_array();
    }
}
