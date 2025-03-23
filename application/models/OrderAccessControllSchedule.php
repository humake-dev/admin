<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class OrderAccessControllSchedule extends SL_SubModel
{
    protected $table = 'order_access_controll_schedules';
    protected $parent_id_name = 'order_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('order_id', 'schedule_date', 'execute');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select = 'oacs.order_id,oacs.schedule_date';
        if (empty($this->order_id)) {
            $this->pdo->select($select);
        } else {
            $this->pdo->select('o.*' . $select);
            $this->pdo->join('orders', 'oacs.order_id=o.id');
            $this->pdo->where(array('oacs.order_id' => $this->order_id));
            $this->pdo->where('(oacs.execute=0 OR oacs.schedule_date>=CURDATE())');
        }
        $query = $this->pdo->get($this->table . ' as oacs', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (!empty($this->order_id)) {
            $this->pdo->join('orders', 'oacs.order_id=o.id');
            $this->pdo->where(array('oacs.order_id' => $this->order_id));
            $this->pdo->where('(oacs.execute=0 OR oacs.schedule_date>=CURDATE())');
        }

        if (isset($id)) {
            $this->pdo->where(array('oacs.id' => $id));
        }

        return $this->pdo->count_all_results($this->table . ' as oacs');
    }
}
