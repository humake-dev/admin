<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Center extends SL_Model
{
    protected $table = 'centers';
    protected $accepted_attributes = array('center_id', 'branch_counts', 'title', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('c.*,GROUP_CONCAT(CONCAT(cp.id,"::",cp.picture_url) ORDER BY cp.id DESC) as picture_url');
        $this->pdo->join('center_pictures as cp', 'cp.center_id=c.id', 'left');

        if ($this->session->userdata('role_id') != 1) {
            $this->pdo->where(array('c.id' => $this->session->userdata('center_id'), 'c.enable' => 1));
        }

        if (!empty($this->title)) {
            $this->pdo->where(array('c.title' => $this->title));
        }

        if (!empty($this->not_id)) {
            $this->pdo->where('c.id !=', $this->not_id);
        }

        if ($this->session->userdata('role_id') != 1) {
            $this->pdo->where(array('c.id' => $this->session->userdata('center_id'), 'c.enable' => 1));
        }

        $this->pdo->order_by($order, $desc);
        $this->pdo->group_by('c.id');
        $query = $this->pdo->get($this->table . ' as c', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('c.id' => $id));
        }

        if (!empty($this->title)) {
            $this->pdo->where(array('c.title' => $this->title));
        }

        if (!empty($this->not_id)) {
            $this->pdo->where('c.id !=', $this->not_id);
        }

        if ($this->session->userdata('role_id') != 1) {
            $this->pdo->where(array('c.id' => $this->session->userdata('center_id'), 'c.enable' => 1));
        }

        return $this->pdo->count_all_results($this->table . ' as c');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('c.*,GROUP_CONCAT(CONCAT(cp.id,"::",cp.picture_url) ORDER BY cp.id DESC) as picture_url');
        $this->pdo->join('center_pictures as cp', 'cp.center_id=c.id', 'left');

        if ($this->session->userdata('role_id') == 1) {
            $this->pdo->where(array('c.id' => $id));
        } else {
            $this->pdo->where(array('c.id' => $this->session->userdata('center_id'), 'c.enable' => 1));
        }

        $query = $this->pdo->get($this->table . ' as c');

        return $query->row_array();
    }

    public function enable($id)
    {
        return $this->pdo->update($this->table, array('enable' => 1), array('id' => $id));
    }

    // 센터는 중지후에 삭제가능
    public function delete($id)
    {
        $content = $this->get_content_data($id);

        if ($content['enable']) {
            return $this->pdo->update($this->table, array('enable' => 0), array('id' => $id));
        } else {
            return parent::delete($id);
        }
    }
}
