<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Admin extends SL_Model
{
    protected $table = 'admins';
    protected $accepted_attributes = array('id', 'picture_url', 'name');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $query = $this->pdo->get($this->table, $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array($this->table . '.id' => $id));
        }

        return $this->pdo->count_all_results($this->table);
    }

    protected function get_content_data($id)
    {
        $this->pdo->select($this->table . '.*');
        $this->pdo->from($this->table);
        $this->pdo->where(array($this->table . '.id' => $id));
        $query = $this->pdo->get();
        return $query->row_array();
    }

    public function update_encrypted_password($password, $id)
    {
        return $this->pdo->update($this->table, array('encrypted_password' => crypt($password . $this->config->item('encryption_key'), '$2a$10$' . substr(md5(time()), 0, 22))), array('id' => $id));
    }

    public function insert(array $data)
    {
        $date = date('Y-m-d H:i:s');
        $data['enable'] = true;
        $data['updated_at'] = $date;
        $data['created_at'] = $date;
        unset($data['password_confirm']);

        $password = $data['password'];

        /* if (empty($crypt)) {
            $password = substr(sha1($data['password']), 0, 40);
        } */

        $data['encrypted_password'] = crypt($password . $this->config->item('encryption_key'), '$2a$10$' . substr(md5(time()), 0, 22));
        unset($data['password']);

        foreach ($data as $key => $value) {
            if (in_array($key, $this->accepted_attributes)) {
                $filtered_data[$key] = $value;
            }
        }

        if ($this->pdo->insert($this->table, $filtered_data)) {
            return $this->pdo->insert_id();
        } else {
            return false;
        }
    }

    public function check_exists_email($email)
    {
        $this->pdo->where(array('email' => $email));

        if ($this->pdo->count_all_results('users')) {
            return true;
        } else {
            return false;
        }
    }
}
