<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Permission extends SL_Model
{
    protected $table = 'permissions';
    protected $accepted_attributes = array('title', 'controller', 'action', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->order_by($order, $desc);

        $query = $this->pdo->get($this->table . ' as c', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('c.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as c');
    }

    public function get_permission_index($per_page = 10000, $page = 0)
    {
        $permission_show_list = $this->get_show_list($per_page, $page);
        $permission_all_list = $this->get_index($per_page, $page);

        foreach ($permission_show_list['list'] as $index => $list1) {
            $permission_show_list['list'][$index]['detail_list']['total'] = 0;
            foreach ($permission_all_list['list'] as $list2) {
                if ($list1['controller'] == $list2['controller']) {
                    if ($list2['show_list'] or $list1['action'] != 'manage') {
                        continue;
                    }
                    $permission_show_list['list'][$index]['detail_list']['total']++;
                    $permission_show_list['list'][$index]['detail_list']['list'][] = $list2;
                }
            }
        }

        return $permission_show_list;
    }

    public function get_show_list($per_page = 1000, $page = 0)
    {
        $result = array();
        $this->pdo->where(array('p.show_list' => 1));
        $result['total'] = $this->pdo->count_all_results($this->table . ' as p');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->where(array('p.show_list' => 1));
        $this->pdo->order_by('p.id', 'asc');
        $query = $this->pdo->get($this->table . ' as p', $per_page, $page);
        $result['list'] = $query->result_array();
        return $result;
    }
}
