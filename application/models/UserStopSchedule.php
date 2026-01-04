<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class UserStopSchedule extends SL_SubModel
{
    protected $table = 'user_stop_schedules';
    protected $parent_id_name = 'user_stop_id';
    protected $accepted_attributes = array('user_stop_id', 'schedule_date', 'enable');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if (empty($this->user_id)) {
            $this->pdo->select('uss.user_stop_id,uss.schedule_date,us.request_date');
        } else {
            $this->pdo->select('us.stop_day_count,us.stop_start_date,us.stop_end_date,us.enable,us.created_at,uss.*,us.request_date,usc.id as content_id,usc.content');
        }

        $this->pdo->join('user_stops as us', 'uss.user_stop_id=us.id');
        $this->pdo->join('user_stop_contents as usc', 'usc.user_stop_id = us.id', 'left');
        $this->pdo->where(array('us.user_id' => $this->user_id, 'uss.enable' => 1));
        $this->pdo->where('us.enable=0 AND uss.schedule_date>=CURDATE()');

        $query = $this->pdo->get($this->table . ' as uss', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (!empty($this->user_id)) {
            $this->pdo->join('user_stops as us', 'uss.user_stop_id=us.id');
            $this->pdo->join('user_stop_contents as usc', 'usc.user_stop_id = us.id', 'left');
            $this->pdo->where(array('us.user_id' => $this->user_id, 'uss.enable' => 1));
            $this->pdo->where('us.enable=0 AND uss.schedule_date>=CURDATE()');
        }

        if (isset($id)) {
            $this->pdo->where(array('uss.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as uss');
    }
}
