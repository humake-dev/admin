<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class AccountRent extends SL_Model
{
    protected $table = 'account_orders';

    public function get_count($id = null)
    {
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('orders as o', 'ao.order_id=o.id');
        $this->pdo->join('rents as r', 'r.order_id=o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');

        if (!empty($this->user_id)) {
            $this->pdo->where(array('a.user_id' => $this->user_id));
        }

        if (!empty($this->product_id)) {
            $this->pdo->where('op.product_id', $this->product_id);
        }

        if (!empty($this->product_ids)) {
            $this->pdo->where_in('op.product_id', $this->product_ids);
        }

        if (!empty($this->transaction_date)) {
            $this->pdo->where('a.transaction_date', $this->transaction_date);
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        return $this->pdo->count_all_results($this->table . ' as ao');
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('orders as o', 'ao.order_id=o.id');
        $this->pdo->join('rents as r', 'r.order_id=o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');

        if (!empty($this->user_id)) {
            $this->pdo->where(array('a.user_id' => $this->user_id));
        }

        if (!empty($this->product_id)) {
            $this->pdo->where('op.product_id', $this->product_id);
        }

        if (!empty($this->product_ids)) {
            $this->pdo->where_in('op.product_id', $this->product_ids);
        }

        if (!empty($this->transaction_date)) {
            $this->pdo->where('a.transaction_date', $this->transaction_date);
        }

        $query = $this->pdo->get($this->table . ' as ao', $per_page, $page);
        return $query->result_array();
    }

    protected function get_content_data($id)
    {
        /*
        $this -> pdo -> where(array($this -> table . '.id' => $id));
        $query = $this -> pdo -> get($this -> table); */

        return $query->row_array();
    }
}
