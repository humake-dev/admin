<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Acl extends CI_Model
{
    public function __construct()
    {
        $this->pdo = $this->load->database('pdo', true);
    }

    public function has_permission($controller, $action = 'read', $role_id = null, $admin_id = null)
    {
        $permission = false;

        if (empty($role_id)) {
            $role_id = $this->session->userdata('role_id');
        }
        $permission = $this->check_role_permission($controller, $action, $role_id);

        if (empty($admin_id)) {
            $admin_id = $this->session->userdata('admin_id');
        }

        $permission = $this->check_admin_permission($controller, $action, $admin_id, $permission);

        return $permission;
    }

    public function check_role_permission($controller, $action, $role_id)
    {
        $role_permissions = $this->role_permissions($role_id);

        $permission = false;
        if ($role_permissions['total']) {
            foreach ($role_permissions['list'] as $index => $value) {
                if ($value['controller'] == $controller) {
                    if ($value['action'] == 'manage') {
                        $permission = true;
                        break;
                    }

                    if ($action == 'read') {
                        $permission = true;
                        break;
                    } elseif ($value['action'] == $action) {
                        $permission = true;
                        break;
                    }
                }
            }
        }

        return $permission;
    }

    public function exist_check_admin_permission($admin_id, $permission_id, $deny = 0)
    {
        $this->pdo->where(array('admin_id' => $admin_id, 'permission_id' => $permission_id, 'deny' => $deny));

        return $this->pdo->count_all_results('admin_permissions');
    }

    public function add_admin_permission($admin_id, $permission_id, $deny = 0)
    {
        if ($this->exist_check_admin_permission($admin_id, $permission_id, $deny)) {
            return true;
        }

        $date = date('Y-m-d H:i:s');

        return $this->pdo->insert('admin_permissions', array('admin_id' => $admin_id, 'permission_id' => $permission_id, 'deny' => $deny, 'created_at' => $date, 'updated_at' => $date));
    }

    public function remove_admin_permission($admin_id, $permission_id, $deny = 0)
    {
        if (empty($this->exist_check_admin_permission($admin_id, $permission_id, $deny))) {
            return true;
        }

        return $this->pdo->delete('admin_permissions', array('admin_id' => $admin_id, 'permission_id' => $permission_id, 'deny' => $deny));
    }

    public function role_permissions($role = 0)
    {
        $result = array();
        $this->pdo->join('permissions as p', 'rp.permission_id=p.id');
        $this->pdo->where('rp.role_id', $role);
        $result['total'] = $this->pdo->count_all_results('role_permissions as rp');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('p.*');
        $this->pdo->join('permissions as p', 'rp.permission_id=p.id');
        $this->pdo->where('rp.role_id', $role);
        $query = $this->pdo->get('role_permissions as rp');
        $result['list'] = $query->result_array();

        return $result;
    }

    public function check_admin_permission($controller, $action, $admin_id, $permission)
    {
        $admin_permissions = $this->admin_permissions($admin_id);

        if (empty($admin_permissions['total'])) {
            return $permission;
        }

        foreach ($admin_permissions['list'] as $index => $value) {
            if ($value['controller'] == $controller) {
                if (empty($value['deny'])) {
                    if ($value['action'] == 'manage') {
                        $permission = true;
                        break;
                    }

                    if ($action == 'read') {
                        $permission = true;
                        break;
                    } elseif ($value['action'] == $action) {
                        $permission = true;
                        break;
                    }
                }
            }
        }

        foreach ($admin_permissions['list'] as $index => $value) {
            if ($value['controller'] == $controller) {
                if (!empty($value['deny'])) {
                    if ($value['action'] == 'manage') {
                        $permission = false;
                        break;
                    }

                    if ($value['action'] == $action) {
                        $permission = false;
                        break;
                    }
                }
            }
        }

        return $permission;
    }

    public function admin_permissions($admin = 0)
    {
        $result = array();
        $this->pdo->join('permissions as p', 'ap.permission_id=p.id');
        $this->pdo->where('ap.admin_id', $admin);
        $result['total'] = $this->pdo->count_all_results('admin_permissions as ap');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('p.*,ap.deny');
        $this->pdo->join('permissions as p', 'ap.permission_id=p.id');
        $this->pdo->where('ap.admin_id', $admin);
        $query = $this->pdo->get('admin_permissions as ap');
        $result['list'] = $query->result_array();

        return $result;
    }
}
