<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class ExerciseCategory extends SL_Model
{
    protected $table = 'exercise_categories';
    protected $accepted_attributes = array('title', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('ec.*,GROUP_CONCAT(CONCAT(ecp.id,"::",ecp.picture_url)) as picture_url');
        $this->pdo->join('exercise_category_pictures as ecp', 'ecp.exercise_category_id=ec.id', 'left');
        $this->pdo->order_by($order, $desc);
        $this->pdo->group_by('ec.id');
        $query = $this->pdo->get($this->table . ' as ec', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('ec.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as ec');
    }


    protected function get_content_data($id)
    {
        $this->pdo->select('ec.*,GROUP_CONCAT(CONCAT(ecp.id,"::",ecp.picture_url)) as picture_url');
        $this->pdo->join('exercise_category_pictures as ecp', 'ecp.exercise_category_id=ec.id', 'left');
        $this->pdo->where(array('ec.id' => $id));
        $query = $this->pdo->get($this->table . ' as ec');
        return $query->row_array();
    }
}
