<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class ProductRelation extends SL_Model
{
    protected $table = 'product_relations';
    protected $accepted_attributes = array('product_relation_type_id', 'product_id', 'sub_product_id', 'rel_product_type', 'enable', 'created_at', 'updated_at');

    public function get_count($id = null)
    {
        $this->pdo->join('products as p', 'pr.product_id=p.id');
        $this->pdo->join('products as sp', 'pr.rel_product_id=sp.id', 'left');

        if (isset($id)) {
            $this->pdo->where(array('pr.' . $this->p_id => $id));
            return $this->pdo->count_all_results($this->table . ' as pr');
        }

        if (!empty($this->product_relation_type_id)) {
            $this->pdo->where(array('pr.product_relation_type_id' => $this->product_relation_type_id));
        }

        if (!empty($this->gender)) {
            if ($this->gender == '1') {
                $this->pdo->where_in('p.gender', array(1, 2));
            } else if ($this->gender == '0') {
                $this->pdo->where_in('p.gender', array(0, 2));
            }
        }

        if (!empty($this->display_type)) {
            $this->pdo->where(array('pr.display_type' => $this->display_type));
        }

        if (!empty($this->category_id)) {
            $this->pdo->where(array('p.product_category_id' => $this->category_id));
        }

        if (!empty($this->product_id)) {
            $this->pdo->where(array('pr.product_id' => $this->product_id));
        }

        if (!empty($this->rel_product_id)) {
            $this->pdo->where(array('pr.rel_product_id' => $this->rel_product_id));
        }

        $this->pdo->where(array('p.branch_id' => $this->session->userdata('branch_id'), 'pr.enable' => 1, 'p.enable' => 1));
        return $this->pdo->count_all_results($this->table . ' as pr');
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('pr.*,p.title as product_name,p.price');
        $this->pdo->join('products as p', 'pr.product_id=p.id');
        $this->pdo->join('products as sp', 'pr.rel_product_id=sp.id', 'left');

        if (!empty($this->product_relation_type_id)) {
            $this->pdo->where(array('pr.product_relation_type_id' => $this->product_relation_type_id));
        }

        if (!empty($this->display_type)) {
            $this->pdo->where(array('pr.display_type' => $this->display_type));
        }

        if (!empty($this->gender)) {
            if ($this->gender == '1') {
                $this->pdo->where_in('p.gender', array(1, 2));
            } else if ($this->gender == '0') {
                $this->pdo->where_in('p.gender', array(0, 2));
            }
        }

        if (!empty($this->category_id)) {
            $this->pdo->where(array('p.product_category_id' => $this->category_id));
        }

        if (!empty($this->product_id)) {
            $this->pdo->where(array('pr.product_id' => $this->product_id));
        }

        if (!empty($this->rel_product_id)) {
            $this->pdo->where(array('pr.rel_product_id' => $this->rel_product_id));
        }

        $this->pdo->where(array('p.branch_id' => $this->session->userdata('branch_id'), 'pr.enable' => 1, 'p.enable' => 1));
        $query = $this->pdo->get($this->table . ' as pr', $per_page, $page);
        return $query->result_array();
    }
}
