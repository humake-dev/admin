<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Account.php';

class AccountAnal extends Account
{
    protected $in_out = 'all';

    public function get_anal($per_page = 10000000, $page = 0)
    {
        $result = array();
        $this->pdo->select('count(*)');
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id', 'left');
        $this->pdo->join('orders as o', 'ao.order_id=o.id', 'left');
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id', 'left');

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));
            $this->pdo->where('if(a.id,a.transaction_date,o.transaction_date) >= "' . $this->start_date . '" AND if(a.id,a.transaction_date,o.transaction_date) <= "' . $this->end_date . '"  AND (a.enable is NULL OR a.enable=1) AND if(a.id,a.branch_id=' . $this->session->userdata('branch_id') . ',1=1)');
        } else {
            $this->pdo->join('branches as b', 'a.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
            $this->pdo->where('if(a.id,a.transaction_date,o.transaction_date) >= "' . $this->start_date . '" AND if(a.id,a.transaction_date,o.transaction_date) <= "' . $this->end_date . '"  AND (a.enable is NULL OR a.enable=1)');
        }

        $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));

        $result['total'] = $this->pdo->count_all_results($this->table . ' as a');

        if (!$result['total']) {
            return $result;
        }

        // group_concat(a.id) as ids,
        $this->pdo->select('
        p.id as category_id,
        if(e.id,"course",if(r.id,"facility",if(o.id,"product",null))) as type,
        if(p.id,p.title,null) as product_name,
        if(pc.id,pc.title,null) as product_category,
        SUM(if(o.re_order,0,1)) as new_order,SUM(if(o.re_order,1,0)) as re_order,
        SUM(if(a.account_category_id=7,if(a.cash+a.credit>0,1,0),0)) as delete_enroll,
        SUM(if(a.account_category_id=8,if(a.cash+a.credit>0,1,0),0)) as delete_rent,
        SUM(if(a.account_category_id=20,if(a.cash+a.credit>0,1,0),0)) as delete_order,
        SUM(if(a.account_category_id=22,if(a.cash+a.credit>0,1,0),0)) as delete_point,
        SUM(if(a.account_category_id=24,if(a.cash+a.credit>0,1,0),0)) as delete_other,
        SUM(if(a.type="O" AND a.account_category_id in(7,8,20,24),0,if(a.type="I",a.cash,IF(a.account_category_id in(4,5,6),-(a.cash),0)))) as i_cash,
        SUM(if(a.type="O" AND a.account_category_id in(7,8,20,24),0,if(a.type="I",a.credit,IF(a.account_category_id in(4,5,6),-(a.credit),0)))) as i_credit,
        SUM(if(a.type="O" AND a.account_category_id in(7,8,20,24),a.cash,0)) AS o_cash,
        SUM(if(a.type="O" AND a.account_category_id in(7,8,20,24),a.credit,0)) AS o_credit,

        (select count(*) FROM orders INNER JOIN order_products ON order_products.order_id=orders.id INNER JOIN account_orders ON account_orders.order_id=orders.id INNER JOIN accounts ON account_orders.account_id=accounts.id WHERE accounts.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" AND order_products.product_id=category_id AND orders.enable=1 AND accounts.enable=1 AND accounts.account_category_id in(1,2,19,23) AND (accounts.cash+accounts.credit)>0
        ) as request_counter,

        count(a.id) as account_counter,
        
        SUM(if(a.type="O" AND a.account_category_id in(7,8,20,24),
        (
          if(e.id,1,
          if(r.id,1,
          if(oo.id,1,
          if(o.id,1,0)
            )
          )
        )
       
        )
        ,0))
         as refund_counter

        ');


        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id', 'left');
        $this->pdo->join('orders as o', 'ao.order_id=o.id', 'left');
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id', 'left');

        $this->pdo->join('users as u', 'o.user_id=u.id', 'left');
        $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
        $this->pdo->join('others as oo', 'oo.order_id=o.id', 'left');        

        $this->pdo->join('account_products as ap', 'ap.account_id=a.id', 'left');
        $this->pdo->join('products as p', 'ap.product_id=p.id', 'left');
        $this->pdo->join('product_categories as pc', 'p.product_category_id=pc.id', 'left');

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));
            $this->pdo->where('if(a.id,a.transaction_date,o.transaction_date) >= "' . $this->start_date . '" AND if(a.id,a.transaction_date,o.transaction_date) <= "' . $this->end_date . '"  AND (a.enable is NULL OR a.enable=1) AND if(a.id,a.branch_id=' . $this->session->userdata('branch_id') . ',1=1)');
        } else {
            $this->pdo->join('branches as b', 'a.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
            $this->pdo->where('if(a.id,a.transaction_date,o.transaction_date) >= "' . $this->start_date . '" AND if(a.id,a.transaction_date,o.transaction_date) <= "' . $this->end_date . '"  AND (a.enable is NULL OR a.enable=1)');
        }

        $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
        $this->pdo->group_by('category_id');

        $query = $this->pdo->get($this->table . ' as a', $per_page, $page);
        $result['list'] = $query->result_array();

        return $result;
    }    

    public function get_product_content($id = null, $per_page=10, $page=0)
    {
        $result = array();
        $this->pdo->select('count(*)');
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id');
        $this->pdo->join('users as u', 'a.user_id=u.id', 'left');
        $this->pdo->join('user_fcs as ufc', 'ufc.user_id=u.id', 'left');
        $this->pdo->join('admins as fc', 'ufc.fc_id=fc.id', 'left');

        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id', 'left');
        $this->pdo->join('orders as o', 'ao.order_id=o.id', 'left');
        $this->pdo->join('account_products as ap', 'ap.account_id=a.id', 'left');

        $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');

        $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'ap.product_id' => $id, 'a.enable' => 1));

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        if ($this->in_out != 'all') {
            if ($this->in_out == 'in') {
                $this->pdo->where(array('a.type' => 'I'));
            }

            if ($this->in_out == 'out') {
                $this->pdo->where(array('a.type' => 'O'));
                $this->pdo->where_in('a.account_category_id',array(7,8,24));             
            }
        }

        $result['total'] = $this->pdo->count_all_results($this->table . ' as a');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('a.*,u.name as user_name,ac.title as account_category_name');
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id');
        $this->pdo->join('users as u', 'a.user_id=u.id', 'left');
        $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id', 'left');
        $this->pdo->join('admins as fc', 'ufc.fc_id=fc.id', 'left');

        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id', 'left');
        $this->pdo->join('orders as o', 'ao.order_id=o.id', 'left');
        $this->pdo->join('account_products as ap', 'ap.account_id=a.id', 'left');

        $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
        $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'ap.product_id' => $id, 'a.enable' => 1));

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        if ($this->in_out != 'all') {
            if ($this->in_out == 'in') {
                $this->pdo->where(array('a.type' => 'I'));
            }

            if ($this->in_out == 'out') {
                $this->pdo->where(array('a.type' => 'O'));
                $this->pdo->where_in('a.account_category_id',array(7,8,24));                
            }
        }

        $this->pdo->order_by('a.id desc');
        $query = $this->pdo->get($this->table . ' as a', $per_page, $page);
        $result['list'] = $query->result_array();

        return $result;
    }

    public function get_total_commision($employee_id = null)
    {
        $this->pdo->select('SUM(cash) as total_commission');
        $this->pdo->join('account_commissions as ac', 'ac.account_id=a.id');

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));

        if (!empty($employee_id)) {
            $this->pdo->where(array('ac.employee_id' => $employee_id));
            $this->pdo->group_by('ac.employee_id');
        }

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        $query = $this->pdo->get($this->table . ' as a');
        $total_commission_a = $query->result_array();

        if (count($total_commission_a)) {
            $total_commission = $total_commission_a[0]['total_commission'];
        } else {
            $total_commission = 0;
        }

        return $total_commission;
    }
}
