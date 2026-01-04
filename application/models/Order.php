<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Order extends SL_Model
{
    protected $table = 'orders';
    protected $table_content = 'order_contents';
    protected $table_content_required = false;
    protected $table_id_name = 'order_id';
    protected $accepted_attributes = array('branch_id', 'user_id', 'transaction_date', 'original_price', 'price', 'payment', 're_order', 'stopped', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select = 'if(oo.id,group_concat(distinct oo.title),group_concat(distinct p.title)) as product_name,op.product_id,o.*,o.transaction_date as order_t_date,u.name,uac.card_no,r.no,r.start_datetime,r.end_datetime,
        (SELECT SUM(if(STRCMP(a.type,"I")>0,-(a.cash+a.credit),(a.cash+a.credit))) FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=o.id and a.enable=1) as total_account,
        IF(r.id,"Rent",IF(e.id,"Enroll","Product")) as order_type,
        IF(r.id,DATE(r.start_datetime),IF(e.id,e.start_date,null)) as start_date,
        IF(r.id,DATE(r.end_datetime),IF(e.id,e.end_date,null)) as end_date,
        IF(r.id,TIMEDIFF(r.end_datetime,r.start_datetime),null) as period_time,
        (SELECT a.transaction_date FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=o.id ORDER BY a.id LIMIT 1) as transaction_date
        ';

        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id=p.id');
        $this->pdo->join('users as u', 'o.user_id=u.id', 'left');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');
        $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
        $this->pdo->join('others as oo', 'oo.order_id=o.id', 'left');

        if (!empty($this->account)) {
            $select.=',a.type,a.cash,a.credit';
            $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
            $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        }

        if(isset($this->fc)) {
            $select.=',fc.name as fc_name';
            $this->pdo->join('user_fcs as ufc', 'ufc.user_id=u.id', 'left');
            $this->pdo->join('admins as fc', 'ufc.fc_id=fc.id', 'left');
        }

        if (isset($this->user_id)) {
            if (is_array($this->user_id)) {
                $this->pdo->where_in('o.user_id', $this->user_id);
            } else {
                $this->pdo->where(array('o.user_id' => $this->user_id));
            }
        }

        if (isset($this->product_id)) {
            if (is_array($this->product_id)) {
                $this->pdo->where_in('op.product_id', $this->product_id);
            } else {
                $this->pdo->where(array('op.product_id' => $this->product_id));
            }
        }

        if (isset($this->start_date)) {
            $this->pdo->where('o.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('o.transaction_date <=', $this->end_date);
        }

        if (!empty($this->get_current_only)) {
            $this->pdo->join('courses as c', 'e.course_id=c.id', 'left');
            $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');
            $this->pdo->where('
            if(e.id,(((c.lesson_type!=4 AND e.start_date <= CURDATE() AND e.end_date >= CURDATE()) OR (c.lesson_type=4 AND (e.quantity-e.use_quantity)>0) OR o.stopped=1) AND oe.id is NULL),
            if(r.id,(((r.start_datetime < NOW() AND r.end_datetime > NOW()) OR o.stopped=1) AND oe.id is NULL),1=1))
            ');
        }

        if (isset($this->stopped)) {
            if ($this->stopped) {
                $this->pdo->where(array('o.stopped' => 1));
            } else {
                $this->pdo->where(array('o.stopped' => 0));
            }
        }

        if (empty($this->enable)) {
            $this->pdo->where(array('o.enable' => 1));
        } else {
            if ($this->enable == 'account') {
                $this->pdo->where('(o.enable=1 OR EXISTS(SELECT order_id FROM account_orders INNER JOIN accounts ON account_orders.account_id=accounts.id WHERE account_orders.order_id=o.id AND accounts.enable=1))');
            } else {
                if ($this->enable != 'all') {
                    $this->pdo->where(array('o.enable' => 0));
                }
            }
        }

        $this->pdo->select($select);
        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->order_by($order, $desc);
        $this->pdo->group_by('o.id');
        $query = $this->pdo->get($this->table . ' as o', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*) as count');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id=p.id');

        if (isset($id)) {
            $this->pdo->where(array('o.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as o');
        }

        if (!empty($this->account)) {
            $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
            $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        }

        if (isset($this->user_id)) {
            if (is_array($this->user_id)) {
                $this->pdo->where_in('o.user_id', $this->user_id);
            } else {
                $this->pdo->where(array('o.user_id' => $this->user_id));
            }
        }

        if (isset($this->product_id)) {
            if (is_array($this->product_id)) {
                $this->pdo->where_in('op.product_id', $this->product_id);
            } else {
                $this->pdo->where(array('op.product_id' => $this->product_id));
            }
        }

        if (isset($this->start_date)) {
            $this->pdo->where('o.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('o.transaction_date <=', $this->end_date);
        }

        if (!empty($this->get_current_only)) {
            $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
            $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
            $this->pdo->join('courses as c', 'e.course_id=c.id', 'left');
            $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');
            $this->pdo->where('
            if(e.id,(((c.lesson_type!=4 AND e.start_date <= CURDATE() AND e.end_date >= CURDATE()) OR (c.lesson_type=4 AND (e.quantity-e.use_quantity)>0) OR o.stopped=1) AND oe.id is NULL),
            if(r.id,(((r.start_datetime < NOW() AND r.end_datetime > NOW()) OR o.stopped=1) AND oe.id is NULL),1=1))
            ');
        }

        if (isset($this->stopped)) {
            if ($this->stopped) {
                $this->pdo->where(array('o.stopped' => 1));
            } else {
                $this->pdo->where(array('o.stopped' => 0));
            }
        }

        if (empty($this->enable)) {
            $this->pdo->where(array('o.enable' => 1));
        } else {
            if ($this->enable == 'account') {
                $this->pdo->where('(o.enable=1 OR EXISTS(SELECT order_id FROM account_orders INNER JOIN accounts ON account_orders.account_id=accounts.id WHERE account_orders.order_id=o.id AND accounts.enable=1))');
            } else {
                if ($this->enable != 'all') {
                    $this->pdo->where(array('o.enable' => 0));
                }
            }
        }

        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->group_by('o.id');

        return $this->pdo->count_all_results($this->table . ' as o');
    }

    public function __set($key, $value)
    {
        parent::__set($key, $value);

        if ($key == 'start_date') {
        }

        if ($key == 'end_date') {
        }
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('p.title as product_name,o.*,op.order_id,u.name as user_name,uac.card_no,op.product_id,
        IF(r.id,DATE(r.start_datetime),IF(e.id,e.start_date,IF(sws.id,sws.start_date,null))) as start_date,
        IF(r.id,DATE(r.end_datetime),IF(e.id,e.end_date,IF(sws.id,sws.end_date,null))) as end_date
        ');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id=p.id');

        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');
        $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
        $this->pdo->join('rent_sws as sws', 'sws.order_id=o.id', 'left');

        $this->pdo->where(array('o.' . $this->p_id => $id));
        $query = $this->pdo->get($this->table . ' as o');

        return $query->row_array();
    }

    public function get_total()
    {
        $this->pdo->select('
        SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit))) as total,
        SUM(if(a.type="I",(a.cash),-(a.cash))) as cash,
        SUM(if(a.type="I",(a.credit),-(a.credit))) as credit
        ');

        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');

        if (isset($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (isset($this->product_id)) {
            $this->pdo->where(array('op.product_id' => $this->product_id));
        }

        if (isset($this->start_date)) {
            $this->pdo->where('o.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('o.transaction_date <=', $this->end_date);
        }

        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'o.enable' => 1));
        $query = $this->pdo->get($this->table . ' as o');

        return $query->result_array();
    }

    public function get_content_by_account_id($account_id)
    {
        $this->pdo->select('o.*');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->where(array('ao.account_id' => $account_id));

        $query = $this->pdo->get($this->table . ' as o');
        return $query->row_array();
    }

    public function resume_by_user_id($user_id)
    {
        return $this->pdo->update($this->table, array('stopped' => 0, 'updated_at' => $this->now), array('user_id' => $user_id));
    }

    // 주문 기록은 실제로는 지우지 않도록 한다.
    public function delete($id)
    {
        return $this->pdo->update($this->table, array('enable' => 0, 'updated_at' => $this->now), array('id' => $id));
    }
}
