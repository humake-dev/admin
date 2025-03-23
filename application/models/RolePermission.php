<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class RolePermission extends SL_Model
{
    protected $table = 'role_permissions';
    protected $accepted_attributes = array('role_id', 'permission_id', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('rp.id,r.title as role_name,p.title as permission_name');
        $this->pdo->join('roles as r', 'rp.role_id=r.id');
        $this->pdo->join('permissions as p', 'rp.permission_id=p.id');
        $query = $this->pdo->get($this->table . ' as rp', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('roles as r', 'rp.role_id=r.id');
        $this->pdo->join('permissions as p', 'rp.permission_id=p.id');
        if (isset($id)) {
            $this->pdo->where(array('rp.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as rp');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('rp.*,r.title as role_name');
        $this->pdo->join('roles as r', 'rp.role_id=r.id');
        $this->pdo->where(array('rp.id' => $id));
        $query = $this->pdo->get($this->table . ' as rp');
        return $query->row_array();
    }
}
