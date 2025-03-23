<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class EmployeePicture extends SL_Model
{
    protected $table = 'admin_pictures';
    protected $accepted_attributes = array('admin_id', 'picture_url', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if (isset($this->employee_id)) {
            $this->pdo->where(array($this->table . '.admin_id' => $this->employee_id));
        }

        $query = $this->pdo->get($this->table, $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array($this->table . '.id' => $id));
        }

        if (isset($this->employee_id)) {
            $this->pdo->where(array($this->table . '.admin_id' => $this->employee_id));
        }

        return $this->pdo->count_all_results($this->table);
    }

    public function insert(array $data)
    {
        $data = array_merge($this->get_default_data(), $data);
        $filtered_data = array();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->accepted_attributes)) {
                $filtered_data[$key] = $value;
            }
        }

        $this->employee_id = $data['admin_id'];
        if ($this->get_count()) {
            return $this->pdo->update($this->table, $filtered_data, array('admin_id' => $this->employee_id));
        } else {
            if ($this->pdo->insert($this->table, $filtered_data)) {
                $id = $this->pdo->insert_id();

                return $id;
            } else {
                return false;
            }
        }
    }
}
