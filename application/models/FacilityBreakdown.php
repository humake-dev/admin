<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class FacilityBreakdown extends SL_Model
{
    protected $table = 'facility_breakdowns';
    protected $accepted_attributes = array('facility_id', 'no', 'description', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if (!empty($this->facility_id)) {
            $this->pdo->where(array($this->table . '.facility_id' => $this->facility_id));
        }

        $query = $this->pdo->get($this->table, $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array($this->table . '.id' => $id));
        }

        if (!empty($this->facility_id)) {
            $this->pdo->where(array($this->table . '.facility_id' => $this->facility_id));
        }

        return $this->pdo->count_all_results($this->table);
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('fb.*,p.title as product_name');
        $this->pdo->join('facilities as f', 'fb.facility_id=f.id');
        $this->pdo->join('products as p', 'f.product_id=p.id');
        $this->pdo->where(array('fb.id' => $id));
        $query = $this->pdo->get($this->table . ' as fb');
        return $query->row_array();
    }
}
