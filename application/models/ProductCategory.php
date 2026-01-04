<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class ProductCategory extends SL_Model
{
    protected $table = 'product_categories';
    protected $table_content = 'product_category_contents';
    protected $table_content_required = false;
    protected $table_id_name = 'product_category_id';
    protected $accepted_attributes = array('type', 'branch_id', 'title', 'order_no', 'product_counts', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('pc.*,ccc.id as content_id,ccc.content');
        $this->pdo->join('product_category_contents as ccc', 'ccc.product_category_id = pc.id', 'left');
        $this->pdo->where(array('pc.branch_id' => $this->session->userdata('branch_id'), 'pc.enable' => true));

        if (!empty($this->type)) {
            if (is_array($this->type)) {
                $this->pdo->where_in('type', $this->type);
            } else {
                $this->pdo->where(array('type' => $this->type));
            }
        }

        $this->pdo->order_by($order, $order);
        $query = $this->pdo->get($this->table . ' as pc', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->where(array('pc.branch_id' => $this->session->userdata('branch_id'), 'pc.enable' => true));
        if (isset($id)) {
            $this->pdo->where(array('pc.id' => $id));
        }

        if (!empty($this->type)) {
            if (is_array($this->type)) {
                $this->pdo->where_in('type', $this->type);
            } else {
                $this->pdo->where(array('type' => $this->type));
            }
        }

        return $this->pdo->count_all_results($this->table . ' as pc');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('ec.*');
        $this->pdo->where(array('ec.id' => $id));
        $query = $this->pdo->get($this->table . ' as ec');

        return $query->row_array();
    }
}
