<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class OrderStopLog extends SL_SubModel
{
    protected $table = 'order_stop_logs';
    protected $parent_id_name = 'order_id';
    protected $accepted_attributes = array('order_id', 'stop_start_date', 'stop_end_date', 'origin_end_date', 'change_end_date', 'stop_day_count', 'request_date', 'enable', 'created_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select = 'osl.*,p.title as product_name,oslc.id as content_id,oslc.content,o.user_id';

        if (!empty($this->get_user_stop_id) or !empty($this->user_stop_id)) {
            $select .= ',os.user_stop_id,os.id as order_stop_id';
        }

        $this->pdo->select($select);
        $this->pdo->join('orders as o', 'osl.order_id = o.id');
        $this->pdo->join('order_products as op', 'op.order_id = o.id');
        $this->pdo->join('products as p', 'op.product_id = p.id');
        $this->pdo->join('order_stop_log_contents as oslc', 'oslc.order_stop_log_id=osl.id', 'left');

        if (!empty($this->get_user_stop_id) or !empty($this->user_stop_id)) {
            $this->pdo->join('order_stop_log_order_stops as oslos', 'oslos.order_stop_log_id=osl.id', 'left');
            $this->pdo->join('order_stops as os', 'oslos.order_stop_id=os.id', 'left');
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->user_stop_id)) {
            $this->pdo->where(array('os.user_stop_id' => $this->user_stop_id));
        }

        $this->pdo->where(array('o.enable' => 1, 'osl.enable' => 1, 'o.branch_id' => $this->session->userdata('branch_id')));

        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as osl');

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('orders as o', 'osl.order_id = o.id');
        $this->pdo->join('order_products as op', 'op.order_id = o.id');
        $this->pdo->join('products as p', 'op.product_id = p.id');

        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->get_user_stop_id) or !empty($this->user_stop_id)) {
            $this->pdo->join('order_stop_log_order_stops as oslos', 'oslos.order_stop_log_id=osl.id', 'left');
            $this->pdo->join('order_stops as os', 'oslos.order_stop_id=os.id', 'left');
        }

        if (!empty($this->user_stop_id)) {
            $this->pdo->where(array('os.user_stop_id' => $this->user_stop_id));
        }

        $this->pdo->where(array('o.enable' => 1, 'osl.enable' => 1, 'o.branch_id' => $this->session->userdata('branch_id')));

        return $this->pdo->count_all_results($this->table . ' as osl');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('osl.*,p.title as product_name,oslc.id as content_id,oslc.content,o.user_id');
        $this->pdo->join('orders as o', 'osl.order_id = o.id');
        $this->pdo->join('order_products as op', 'op.order_id = o.id');
        $this->pdo->join('products as p', 'op.product_id = p.id');
        $this->pdo->join('order_stop_log_contents as oslc', 'oslc.order_stop_log_id=osl.id', 'left');
        $this->pdo->where(array('osl.id' => $id));
        $query = $this->pdo->get($this->table . ' as osl');

        return $query->row_array();
    }
}
