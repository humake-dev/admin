<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class OrderContent extends SL_Model
{
    protected $table = 'order_contents';
    protected $accepted_attributes = array('order_id', 'content', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('oc.*,o.user_id,p.title as product_name,pc.title as product_category_name');
        $this->pdo->join('orders as o', 'oc.order_id=o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id=p.id');
        $this->pdo->join('product_categories as pc', 'p.product_category_id=pc.id', 'left');

        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->order_id)) {
            $this->pdo->where(array('o.id' => $this->order_id));
        }

        $this->pdo->where(array('o.enable' => 1));

        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as oc', $per_page, $page);
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('orders as o', 'oc.order_id=o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id=p.id');

        if (isset($id)) {
            $this->pdo->where(array('oc.id' => $id));
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->order_id)) {
            $this->pdo->where(array('o.id' => $this->order_id));
        }

        $this->pdo->where(array('o.enable' => 1));

        return $this->pdo->count_all_results($this->table . ' as oc');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('oc.*,o.user_id,p.title as product_name,pc.title as product_category_name');
        $this->pdo->join('orders as o', 'oc.order_id=o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id=p.id');
        $this->pdo->join('product_categories as pc', 'p.product_category_id=pc.id', 'left');
        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'oc.id' => $id));
        $query = $this->pdo->get($this->table . ' as oc');

        return $query->row_array();
    }
}
