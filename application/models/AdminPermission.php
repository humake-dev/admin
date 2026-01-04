<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class AdminPermission extends SL_Model
{
    protected $table = 'admin_permissions';
    protected $accepted_attributes = array('admin_id', 'permission_id', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('ap.id,a.name as name,p.title as permission_name');
        $this->pdo->join('admins as a', 'ap.admin_id=a.id');
        $this->pdo->join('permissions as p', 'ap.permission_id=p.id');
        $query = $this->pdo->get($this->table . ' as ap', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('admins as a', 'ap.admin_id=a.id');
        $this->pdo->join('permissions as p', 'ap.permission_id=p.id');

        if (isset($id)) {
            $this->pdo->where(array('ap.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as ap');
    }
}