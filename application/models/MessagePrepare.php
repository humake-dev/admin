<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class MessagePrepare extends SL_Model
{
    protected $table = 'message_prepares';
    protected $table_content = 'message_prepare_contents';
    protected $accepted_attributes = array('branch_id', 'title', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('mp.*,mpc.content');
        $this->pdo->join('message_prepare_contents as mpc', 'mpc.id=mp.id');
        $this->pdo->where(array('mp.branch_id' => $this->session->userdata('branch_id'), 'mp.enable' => 1));
        $this->pdo->order_by('mp.id', 'desc');
        $query = $this->pdo->get($this->table . ' as mp', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('message_prepare_contents as mpc', 'mpc.id=mp.id');
        $this->pdo->where(array('mp.branch_id' => $this->session->userdata('branch_id'), 'mp.enable' => 1));

        if (isset($id)) {
            $this->pdo->where(array('mp.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as mp');
    }
}
