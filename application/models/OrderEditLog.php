<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class OrderEditLog extends SL_Model
{
    protected $table = 'order_edit_logs';
    protected $accepted_attributes = array('order_id', 'admin_id', 'revision', 'content', 'enable', 'created_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('oel.*,a.name as editor,u.id as user_id,u.name as user_name,p.title as product_name,(select count(*) FROM order_edit_log_fields WHERE order_edit_log_id=oel.id) as field_change_count');
        $this->pdo->join('orders as o', 'oel.order_id=o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id=p.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('admins as a', 'oel.admin_id=a.id', 'left');

        if (!empty($this->order_id)) {
            $this->pdo->where(array('oel.order_id' => $this->order_id));
        }

        if (!empty($this->start_date)) {
            $this->pdo->where('date(oel.created_at) >=', $this->start_date);
        }

        if (!empty($this->end_date)) {
            $this->pdo->where('date(oel.created_at) <=', $this->end_date);
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->field)) {
            $this->pdo->join('order_edit_log_fields as oelf', 'oelf.order_edit_log_id=oel.id');
            $this->pdo->where(array('oelf.field' => $this->field));
        }

        if (!empty($this->product_id)) {
            $this->pdo->where(array('p.id' => $this->product_id));
        }

        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->order_by($order, $desc);

        $query = $this->pdo->get($this->table . ' as oel', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('orders as o', 'oel.order_id=o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id=p.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');

        if (isset($id)) {
            $this->pdo->where(array('oel.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as oel');
        }

        if (!empty($this->order_id)) {
            $this->pdo->where(array('oel.order_id' => $this->order_id));
        }

        if (!empty($this->start_date)) {
            $this->pdo->where('date(oel.created_at) >=', $this->start_date);
        }

        if (!empty($this->end_date)) {
            $this->pdo->where('date(oel.created_at) <=', $this->end_date);
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->field)) {
            $this->pdo->join('order_edit_log_fields as oelf', 'oelf.order_edit_log_id=oel.id');
            $this->pdo->where(array('oelf.field' => $this->field));
        }

        if (!empty($this->product_id)) {
            $this->pdo->where(array('p.id' => $this->product_id));
        }

        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));

        return $this->pdo->count_all_results($this->table . ' as oel');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('oel.*,a.name as editor,p.title as product_name,(select count(*) FROM order_edit_log_fields WHERE order_edit_log_id=oel.id) as field_change_count');
        $this->pdo->join('orders as o', 'oel.order_id=o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id=p.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('admins as a', 'oel.admin_id=a.id', 'left');
        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'oel.id' => $id));
        $query = $this->pdo->get($this->table . ' as oel');

        return $query->row_array();
    }

    public function get_user_list()
    {
        $result = array();
        $result['total'] = $this->get_user_count();

        if (!$result['total']) {
            return $result;
        }

        $result['list'] = $this->get_user_list_data();

        return $result;
    }

    protected function get_user_count()
    {
        $this->pdo->select('count(*)');
        $this->pdo->join('orders as o', 'oel.order_id=o.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->group_by('u.id');

        return $this->pdo->count_all_results($this->table . ' as oel');
    }

    protected function get_user_list_data()
    {
        $this->pdo->select('u.id,u.name');
        $this->pdo->join('orders as o', 'oel.order_id=o.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->group_by('u.id');

        $query = $this->pdo->get($this->table . ' as oel');

        return $query->result_array();
    }
}
