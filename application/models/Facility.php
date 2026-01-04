<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Facility extends SL_Model
{
    protected $table = 'facilities';
    protected $accepted_attributes = array('product_id', 'type', 'order_no', 'quantity', 'start_no', 'use_not_set');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('f.*,p.price,p.title,p.branch_id,p.gender,pc.id as content_id,pc.content,p.enable,p.created_at,p.updated_at');
        $this->pdo->join('products as p', 'f.product_id=p.id');
        $this->pdo->join('product_contents as pc', 'pc.product_id=p.id', 'left');

        if (isset($this->gender)) {
            if ($this->gender == '1') {
                $this->pdo->where_in('p.gender', array(1, 2));
            } elseif ($this->gender == '0') {
                $this->pdo->where_in('p.gender', array(0, 2));
            }
        }

        if (empty($this->branch_id)) {
            $this->pdo->where(array('p.branch_id' => $this->session->userdata('branch_id'), 'p.enable' => 1));
        } else {
            $this->pdo->where(array('p.branch_id' => $this->branch_id, 'p.enable' => 1));
        }

        $this->pdo->order_by('f.order_no', 'asc');
        $query = $this->pdo->get($this->table . ' as f', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('products as p', 'f.product_id=p.id');

        if (isset($id)) {
            $this->pdo->where(array('f.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as f');
        }

        if (isset($this->gender)) {
            if ($this->gender == '1') {
                $this->pdo->where_in('p.gender', array(1, 2));
            } elseif ($this->gender == '0') {
                $this->pdo->where_in('p.gender', array(0, 2));
            }
        }

        if (empty($this->branch_id)) {
            $this->pdo->where(array('p.branch_id' => $this->session->userdata('branch_id'), 'p.enable' => 1));
        } else {
            $this->pdo->where(array('p.branch_id' => $this->branch_id, 'p.enable' => 1));
        }

        return $this->pdo->count_all_results($this->table . ' as f');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('f.*,p.price,p.title,p.title as product_name,p.branch_id,p.gender,pc.id as content_id,pc.content,p.enable,p.created_at,p.updated_at');
        $this->pdo->join('products as p', 'f.product_id=p.id');
        $this->pdo->join('product_contents as pc', 'pc.product_id=p.id', 'left');
        $this->pdo->where(array('f.id' => $id));

        $query = $this->pdo->get($this->table . ' as f');

        return $query->row_array();
    }

    final public function get_content_by_product_id($id)
    {
        $result = $this->get_content_by_product_id_data($id);

        if (!is_array($result)) {
            return false;
        }

        if (!count($result)) {
            return false;
        }

        return $result;
    }

    protected function get_content_by_product_id_data($product_id)
    {
        $this->pdo->select('f.*,p.price,p.title,p.branch_id,p.gender,pc.id as content_id,pc.content,p.enable,p.created_at,p.updated_at');
        $this->pdo->join('products as p', 'f.product_id=p.id');
        $this->pdo->join('product_contents as pc', 'pc.product_id=p.id', 'left');
        $this->pdo->where(array('f.product_id' => $product_id));

        $query = $this->pdo->get($this->table . ' as f');

        return $query->row_array();
    }

    // 상품은 실제로는 지우지 않도록 한다.
    public function delete($id)
    {
        $content = $this->get_content_data($id);

        return $this->pdo->update('products', array('enable' => 0, 'updated_at' => $this->now), array('id' => $content['product_id']));
    }
}
