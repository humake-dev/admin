<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AdminLogin extends CI_Model
{
    protected $table = 'admins';

    public function __construct()
    {
        $this->pdo = $this->load->database('pdo', true);
    }

    public function login($id, $password, $crypt = false)
    {
        $this->pdo->where(array('uid' => $id));
        if (!$this->pdo->count_all_results($this->table)) {
            return false;
        }

        $this->pdo->select('a.*,c.id as center_id,c.title as center_name,b.title as branch_name,b.id as branch_id,ap.picture_url');
        $this->pdo->join('branches as b', 'a.branch_id=b.id');
        $this->pdo->join('centers as c', 'b.center_id=c.id');
        $this->pdo->join('admin_pictures as ap', 'ap.admin_id=a.id', 'left');
        $this->pdo->where(array('uid' => $id, 'c.enable' => 1, 'b.enable' => 1, 'a.enable' => 1));

        $query = $this->pdo->get($this->table . ' as a');
        $content = $query->row_array();

        /*if ($crypt) {
            $password = substr(sha1($password), 0, 40);
        } */
        $encrypted_password = crypt($password . $this->config->item('encryption_key'), substr($content['encrypted_password'], 0, 29));

        if (strcmp($content['encrypted_password'], $encrypted_password)) {
            return false;
        }

        return $content;
    }
}
