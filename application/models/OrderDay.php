<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class OrderDay extends SL_Model
{
    protected $table = 'accounts';
    protected $table_id_name = 'account_id';

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select = 'a.type,(a.cash+a.credit) as payment,a.cash,a.credit,fc.name as fc_name,u.name,p.title as product_name,IF(r.id,"Rent",IF(e.id,"Enroll","Product")) as order_type,a.user_id,a.created_at';

        $this->pdo->join('account_products as ap', 'ap.account_id=a.id');
        $this->pdo->join('products as p', 'ap.product_id=p.id');
        $this->pdo->join('users as u', 'a.user_id=u.id');
        $this->pdo->join('user_fcs as ufc', 'ufc.user_id=u.id', 'left');
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id','left');
        $this->pdo->join('orders as o', 'ao.order_id=o.id', 'left');        
        $this->pdo->join('admins as fc', 'ufc.fc_id=fc.id', 'left');
        $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
        $this->pdo->join('others as oo', 'oo.order_id=o.id', 'left');        

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        $this->pdo->select($select);
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'),'a.enable'=>1));
        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as a', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*) as count');
        $this->pdo->join('account_products as ap', 'ap.account_id=a.id');
        $this->pdo->join('products as p', 'ap.product_id=p.id');

        if (isset($id)) {
            $this->pdo->where(array('a.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as o');
        }

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'),'a.enable'=>1));

        return $this->pdo->count_all_results($this->table . ' as a');
    }

    public function get_total()
    {
        $this->pdo->select('
        SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit))) as total,
        SUM(if(a.type="I",(a.cash),-(a.cash))) as cash,
        SUM(if(a.type="I",(a.credit),-(a.credit))) as credit
        ');

        $this->pdo->join('account_products as ap', 'ap.account_id=a.id');
        $this->pdo->join('products as p', 'ap.product_id=p.id');

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'),'a.enable'=>1));
        $query = $this->pdo->get($this->table . ' as a');

        return $query->result_array();
    }    
}
