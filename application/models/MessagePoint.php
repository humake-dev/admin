<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class MessagePoint extends SL_Model
{
    protected $table = 'branches';
    protected $accepted_attributes = array('branch_id', 'phone', 'sms_available_point', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
            if(empty($this->branch_id)) {
                if ($this->session->userdata('center_id')) {
                    $this->pdo->where(array('b.enable' => 1));
                } else {
                $this->pdo->where(array('b.id' => $this->session->userdata('branch_id'), 'b.enable' => 1));
                }
            } else {
                $this->pdo->where(array('b.enable' => 1));
                $this->pdo->where_in('b.id',$this->branch_id);
            }

        $this->pdo->where(array('b.enable' => 1));
        $query = $this->pdo->get($this->table . ' as b', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('b.id' => $id));
        } else {
            if(empty($this->branch_id)) {
                if ($this->session->userdata('center_id')) {
                    $this->pdo->where(array('b.enable' => 1));
                } else {
                $this->pdo->where(array('b.id' => $this->session->userdata('branch_id'), 'b.enable' => 1));
                }
            } else {
                $this->pdo->where(array('b.enable' => 1));
                $this->pdo->where_in('b.id',$this->branch_id);
            }
        }

        $this->pdo->where(array('b.enable' => 1));
        return $this->pdo->count_all_results($this->table . ' as b');
    }

    public function delete($id)
    {
        return $this->pdo->update($this->table, array('sms_available_point' => 0, 'updated_at' => $this->now), array('id' => $id));
    }
}
