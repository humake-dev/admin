<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Exercise extends SL_Model
{
    protected $table = 'exercises';
    protected $accepted_attributes = array('exercise_category_id', 'title', 'content', 'tip', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('e.*,ec.title as category_name,GROUP_CONCAT(CONCAT(ep.id,"::",ep.picture_url)) as picture_url');
        $this->pdo->join('exercise_pictures as ep', 'ep.exercise_id=e.id', 'left');
        $this->pdo->join('exercise_categories as ec', 'e.exercise_category_id=ec.id');
        $this->pdo->where(array('e.exercise_category_id' => $this->category_id));
        $this->pdo->group_by('e.id');

        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as e', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->where(array('e.exercise_category_id' => $this->category_id));

        if (isset($id)) {
            $this->pdo->where(array('e.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as e');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('e.*,ec.title as category_name,GROUP_CONCAT(CONCAT(ep.id,"::",ep.picture_url)) as picture_url');
        $this->pdo->join('exercise_pictures as ep', 'ep.exercise_id=e.id', 'left');
        $this->pdo->join('exercise_categories as ec', 'e.exercise_category_id=ec.id');
        $this->pdo->where(array('e.id' => $id));
        $query = $this->pdo->get($this->table . ' as e');
        return $query->row_array();
    }
}
