<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class ErrorReport extends SL_Model
{
    protected $table = 'error_reports';
    protected $table_content = 'error_report_contents';
    protected $accepted_attributes = array('branch_id', 'admin_id', 'title', 'solve', 'solve_date', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('er.*,b.title as branch_name,a.name as admin_name');
        $this->pdo->join('admins as a', 'er.admin_id=a.id');
        $this->pdo->join('branches as b', 'er.branch_id=b.id');


            if(empty($this->branch_id)) {
                if ($this->session->userdata('center_id')) {
                    $this->pdo->where(array('er.enable' => 1));
                } else {
                    $this->pdo->where(array('er.branch_id' => $this->session->userdata('branch_id'), 'er.enable' => 1));
                }
            } else {
                $this->pdo->where(array('er.enable' => 1));
                $this->pdo->where_in('er.branch_id',$this->branch_id);
            }
            
        $this->pdo->order_by($order, $desc);

        $query = $this->pdo->get($this->table . ' as er', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('admins as a', 'er.admin_id=a.id');

        if (isset($id)) {
            $this->pdo->where(array('er.id' => $id));
        } else {
                if(empty($this->branch_id)) {
                    if ($this->session->userdata('center_id')) {
                        $this->pdo->where(array('er.enable' => 1));
                    } else {
                        $this->pdo->where(array('er.branch_id' => $this->session->userdata('branch_id'), 'er.enable' => 1));
                    }
                } else {
                    $this->pdo->where(array('er.enable' => 1));
                    $this->pdo->where_in('er.branch_id',$this->branch_id);
                }
        }

        return $this->pdo->count_all_results($this->table . ' as er');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('er.*,b.title as branch_name,a.name as admin_name,erc.content');
        $this->pdo->join('admins as a', 'er.admin_id=a.id');
        $this->pdo->join('branches as b', 'er.branch_id=b.id');
        $this->pdo->join('error_report_contents as erc', 'erc.id=er.id');
        $this->pdo->where(array('er.enable' => 1));
        $this->pdo->where(array('er.id' => $id));
        $query = $this->pdo->get($this->table . ' as er');

        return $query->row_array();
    }
}
