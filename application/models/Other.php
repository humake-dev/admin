<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Other extends SL_Model
{
    protected $table = 'others';
    protected $accepted_attributes = array('order_id', 'title');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('oo.*,o.user_id,u.name as user_name,uac.card_no,o.price,a.cash,a.credit,a.transaction_date');
        $this->pdo->join('orders as o', 'oo.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');
        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'o.enable' => 1, 'a.enable'=>1));
        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as oo', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('orders as o', 'oo.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');

        if (isset($id)) {
            $this->pdo->where(array('oo.id' => $id));
        }
        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'o.enable' => 1, 'a.enable'=>1));

        return $this->pdo->count_all_results($this->table . ' as oo');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('oo.*,o.user_id,u.name as user_name,uac.card_no,o.price,a.cash,a.credit,a.transaction_date,o.created_at,o.updated_at');
        $this->pdo->join('orders as o', 'oo.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');
        $this->pdo->where(array('oo.id' => $id));
        $query = $this->pdo->get($this->table . ' as oo');

        return $query->row_array();
    }
}
