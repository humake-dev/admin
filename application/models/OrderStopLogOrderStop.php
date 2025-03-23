<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class OrderStopLogOrderStop extends SL_Model
{
    protected $table = 'order_stop_log_order_stops';
    protected $accepted_attributes = array('order_stop_log_id', 'order_stop_id');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if (!empty($this->order_stop_id)) {
            $this->pdo->where(array('oslos.order_stop_id' => $this->order_stop_id));
        }

        if (!empty($this->order_stop_log_id)) {
            $this->pdo->where(array('oslos.order_stop_log_id' => $this->order_stop_log_id));
        }
        
        $query = $this->pdo->get($this->table . ' as oslos');
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array('ot.id' => $id));
        }

        if (!empty($this->order_stop_id)) {
            $this->pdo->where(array('oslos.order_stop_id' => $this->order_stop_id));
        }

        if (!empty($this->order_stop_log_id)) {
            $this->pdo->where(array('oslos.order_stop_log_id' => $this->order_stop_log_id));
        }

        return $this->pdo->count_all_results($this->table . ' as oslos');
    }
}
