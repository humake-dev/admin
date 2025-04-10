<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class ExerciseCategoryPicture extends SL_Model
{
    protected $table = 'exercise_category_pictures';
    protected $accepted_attributes = array('exercise_category_id', 'picture_url', 'enable', 'created_at', 'updated_at');

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
}
