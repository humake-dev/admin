<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class UserPicture extends SL_Model
{
    protected $table = 'user_pictures';
    protected $accepted_attributes = array('user_id', 'picture_url', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if (isset($this->user_id)) {
            $this->pdo->where(array($this->table . '.user_id' => $this->user_id));
        }

        $query = $this->pdo->get($this->table, $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array($this->table . '.id' => $id));
        }

        if (isset($this->user_id)) {
            $this->pdo->where(array($this->table . '.user_id' => $this->user_id));
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

        $this->user_id = $data['user_id'];
        if ($this->get_count()) {
            return $this->pdo->update($this->table, $filtered_data, array('user_id' => $this->user_id));
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
