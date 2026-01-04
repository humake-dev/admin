<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Analysis extends SL_Model
{
    protected $table = 'accounts';
    protected $start_date;
    protected $end_date;

    public function __set($key, $value)
    {
        if ($key == 'start_date') {
            $this->start_date = $value;
        }

        if ($key == 'end_date') {
            $this->end_date = $value;
        }

        $this->$key = $value;
    }

    public function get_index($per_page = 0, $page = 0, $order = null, $desc = null, $enable = true)
    {
        // 총 수입
        $this->pdo->select('SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit))) as sum');
        if ($this->session->userdata('branch_id')) {
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
            $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        } else {
            $this->pdo->join('branches as b', 'a.branch_id=b.id');
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id'), 'a.enable' => 1));
        }
        $this->pdo->where('a.transaction_date >= "' . $this->start_date . '" AND a.transaction_date <= "' . $this->end_date . '"');
        $query = $this->pdo->get($this->table . ' as a');
        $result['total_sales'] = $query->row_array()['sum'];

        // 신규 유료 회원권 수
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('product_relations as pr', 'pr.product_id=op.product_id');

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'o.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }

        $this->pdo->where(array('pr.product_relation_type_id' => PRIMARY_COURSE_ID, 'a.account_category_id' => 1, 'o.enable' => 1, 'a.enable' => 1));
        $this->pdo->where('a.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" AND (a.cash+a.credit)>0');
        $result['total_primary'] = $this->pdo->count_all_results('enrolls as e');

        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('courses as c', 'c.product_id=op.product_id');
        $this->pdo->join('users as u', 'o.user_id=u.id', 'left');

        // 신규 유료 PT 수
        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'o.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }

        $this->pdo->where(array('c.lesson_type' => 4, 'a.account_category_id' => 1, 'o.enable' => 1, 'a.enable' => 1));
        $this->pdo->where('a.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" AND (a.cash+a.credit)>0');
        $result['total_pts'] = $this->pdo->count_all_results('enrolls as e');

        // 평균결제금액
        if ($this->session->userdata('branch_id')) {
            $query = $this->pdo->query('SELECT avg(total_amount) as avg FROM (SELECT sum(cash+credit) as total_amount FROM accounts WHERE branch_id=' . $this->session->userdata('branch_id') . ' AND date(created_at) BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" GROUP BY user_id) as cc');
            $result['avg_amount'] = $query->row_array()['avg'];
        } else {
            $query = $this->pdo->query('SELECT avg(total_amount) as avg FROM (SELECT sum(cash+credit) as total_amount FROM accounts as a INNER JOIN branches as b ON a.branch_id=b.id WHERE b.center_id=' . $this->session->userdata('center_id') . ' AND a.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" GROUP BY a.user_id) as cc');
            $result['avg_amount'] = $query->row_array()['avg'];
        }

        // 평균등록횟수
        if ($this->session->userdata('branch_id')) {
            $query = $this->pdo->query('SELECT avg(total_enrolls) as avg FROM
            (SELECT count(*) as total_enrolls
            FROM enrolls as e INNER JOIN orders as o ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id INNER JOIN products as p ON c.product_id=p.id WHERE o.branch_id=' . $this->session->userdata('branch_id') . ' AND o.enable=1 AND DATE(o.created_at) BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" GROUP BY o.user_id) as cc');
            $result['avg_enrolls'] = $query->row_array()['avg'];
        } else {
            $query = $this->pdo->query('SELECT avg(total_enrolls) as avg FROM (SELECT count(*) as total_enrolls FROM enrolls as e INNER JOIN orders as o ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id INNER JOIN products as p ON c.product_id=p.id  INNER JOIN branches as b ON o.branch_id=b.id WHERE b.center_id=' . $this->session->userdata('center_id') . ' AND o.enable=1 AND o.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" GROUP BY o.user_id) as cc');
            $result['avg_enrolls'] = $query->row_array()['avg'];
        }

        // 평균재등록횟수
        if ($this->session->userdata('branch_id')) {
            $query = $this->pdo->query('SELECT avg(total_enrolls) as avg FROM (SELECT count(*) as total_enrolls FROM enrolls as e INNER JOIN orders as o ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id INNER JOIN products as p ON c.product_id=p.id  WHERE re_order=1 AND o.branch_id=' . $this->session->userdata('branch_id') . ' AND o.enable=1 AND DATE(o.created_at) BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" GROUP BY o.user_id) as cc');
            $result['avg_re_enrolls'] = $query->row_array()['avg'];
        } else {
            $query = $this->pdo->query('SELECT avg(total_enrolls) as avg FROM (SELECT count(*) as total_enrolls FROM enrolls as e INNER JOIN orders as o ON e.order_id=o.id INNER JOIN courses as c ON e.course_id=c.id INNER JOIN products as p ON c.product_id=p.id  INNER JOIN branches as b ON o.branch_id=b.id WHERE re_order=1 AND b.center_id=' . $this->session->userdata('center_id') . ' AND o.enable=1 AND o.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" GROUP BY o.user_id) as cc');
            $result['avg_re_enrolls'] = $query->row_array()['avg'];
        }

        $result['sales_count'] = $this->sales_count();
        $result['new_re_ratio'] = $this->new_re_ratio();
        $result['course_count'] = $this->course_count();
        $result['course_new_re_ratio'] = $this->course_new_re_ratio();
        $result['payment_type'] = $this->payment_type();
        $result['entrance_month'] = $this->entrance_month();
        $result['age_count'] = $this->age_count();
        $result['gender_count'] = $this->gender_count();
        $result['entrance_week'] = $this->entrance_week();

        return $result;
    }

    // 매출
    private function sales_count()
    {
        $result = array();
        if ($this->session->userdata('branch_id')) {
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
            $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        } else {
            $this->pdo->join('branches as b', 'a.branch_id=b.id');
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id'), 'a.enable' => 1));
        }

        $this->pdo->where('a.transaction_date BETWEEN ADDDATE(LAST_DAY(date_add("' . $this->start_date . '",interval -3 month)), 1)  AND "' . $this->end_date . '"');
        $result['total'] = $this->pdo->count_all_results($this->table . ' as a');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('concat(month(a.transaction_date),"월") as month,sum(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit))) as sales');
        if ($this->session->userdata('branch_id')) {
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
            $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        } else {
            $this->pdo->join('branches as b', 'a.branch_id=b.id');
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id'), 'a.enable' => 1));
        }
        $this->pdo->where('a.transaction_date BETWEEN ADDDATE(LAST_DAY(date_add("' . $this->start_date . '",interval -3 month)), 1)  AND "' . $this->end_date . '"');
        $this->pdo->group_by('month');
        $query = $this->pdo->get($this->table . ' as a');
        $result['result'] = $query->result_array();

        return $result;
    }

    // 등록비율
    private function new_re_ratio()
    {
        $result = array();

        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('courses as c', 'op.product_id=c.product_id');
        $this->pdo->join('product_relations as pr', 'pr.product_id=op.product_id');

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'o.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }

        $this->pdo->where(array('pr.product_relation_type_id' => PRIMARY_COURSE_ID, 'a.account_category_id' => 1, 'o.enable' => 1, 'a.enable' => 1));
        $this->pdo->where('a.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" AND (a.cash+a.credit)>0');
        $result['total'] = $this->pdo->count_all_results('enrolls as e');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('case
            when o.re_order=0 then "신규등록"
            when o.re_order=1 then "재등록"
            end as resist_type,count(*) as count', false);

        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('courses as c', 'op.product_id=c.product_id');
        $this->pdo->join('product_relations as pr', 'pr.product_id=op.product_id');

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'o.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }

        $this->pdo->where(array('pr.product_relation_type_id' => PRIMARY_COURSE_ID, 'a.account_category_id' => 1, 'o.enable' => 1, 'a.enable' => 1));
        $this->pdo->where('a.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" AND (a.cash+a.credit)>0');
        $this->pdo->group_by('o.re_order');
        $query = $this->pdo->get('enrolls as e');
        $result['result'] = $query->result_array();

        return $result;
    }

    // 등록구분, 강습신청비율
    private function course_count()
    {
        $result = array();

        $this->pdo->select('count(*)');
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('courses as c', 'c.product_id=op.product_id');
        $this->pdo->join('product_relations as pr', 'pr.product_id=op.product_id', 'left');

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'o.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }

        $this->pdo->where(array('a.account_category_id' => 1, 'o.enable' => 1, 'a.enable' => 1));
        $this->pdo->where('(pr.product_relation_type_id=' . PRIMARY_COURSE_ID . ' OR c.lesson_type=4) AND a.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" AND (a.cash+a.credit)>0');
        $result['total'] = $this->pdo->count_all_results('enrolls as e');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('case
            when c.lesson_type=1 then "회원권"
            when c.lesson_type=4 then "PT"
            end as title,count(*) as count', false);
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('courses as c', 'c.product_id=op.product_id');
        $this->pdo->join('product_relations as pr', 'pr.product_id=op.product_id', 'left');

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'o.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }

        $this->pdo->where(array('a.account_category_id' => 1, 'o.enable' => 1, 'a.enable' => 1));
        $this->pdo->where('(pr.product_relation_type_id=' . PRIMARY_COURSE_ID . ' OR c.lesson_type=4) AND a.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" AND (a.cash+a.credit)>0');
        $this->pdo->group_by('lesson_type');
        $query = $this->pdo->get('enrolls as e');
        $result['result'] = $query->result_array();

        return $result;
    }

    // 강습등록횟수
    private function course_new_re_ratio()
    {
        $result = array();

        $this->pdo->select('count(*)');
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('courses as c', 'e.course_id=c.id');
        $this->pdo->join('products as p', 'c.product_id=p.id');

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('p.branch_id' => $this->session->userdata('branch_id'), 'o.enable' => 1));
        } else {
            $this->pdo->join('branches as b', 'p.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id'), 'o.enable' => 1));
        }

        $this->pdo->where('DATE(o.created_at) BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '"');
        $this->pdo->group_by('o.re_order');
        $result['total'] = $this->pdo->count_all_results('enrolls as e');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('p.title,case when re_order=1 then count(*) else 0 end as new_count,case when re_order=1 then count(*) else 0 end as re_count', false);
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('courses as c', 'e.course_id=c.id');
        $this->pdo->join('products as p', 'c.product_id=p.id');
        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('p.branch_id' => $this->session->userdata('branch_id'), 'o.enable' => 1));
        } else {
            $this->pdo->join('branches as b', 'p.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id'), 'o.enable' => 1));
        }
        $this->pdo->where('DATE(o.created_at) BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '"');
        $this->pdo->group_by('o.re_order');
        $query = $this->pdo->get('enrolls as e');
        $result['result'] = $query->result_array();

        return $result;
    }

    // 결제형태
    private function payment_type()
    {
        $result = array();

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
            $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1, 'a.type' => 'I'));
        } else {
            $this->pdo->join('branches as b', 'a.branch_id=b.id');
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id'), 'a.enable' => 1, 'a.type' => 'I'));
        }
        $this->pdo->where('(a.cash!=0 OR a.credit!=0)');
        $this->pdo->where('a.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '"');
        $result['total'] = $this->pdo->count_all_results($this->table . ' as a');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('if(a.cash>0 and a.credit>0,"혼합결제",if(a.credit>0,"카드결제",if(a.cash>0,"현금결제","미결제"))) as payment_type,SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit))) as count', false);
        if ($this->session->userdata('branch_id')) {
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
            $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        } else {
            $this->pdo->join('branches as b', 'a.branch_id=b.id');
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id'), 'a.enable' => 1));
        }
        $this->pdo->where('a.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '"');
        $this->pdo->group_by('payment_type');
        $query = $this->pdo->get($this->table . ' as a');
        $result['result'] = $query->result_array();

        return $result;
    }

    // 3개월간 월별입장회원
    private function entrance_month()
    {
        $result = array();

        $this->pdo->join('users as u', 'me.user_id=u.id');
        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'u.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }
        $this->pdo->where('date(me.created_at) BETWEEN ADDDATE(LAST_DAY(date_add("' . $this->end_date . '",interval -3 month)), 1) AND "' . $this->end_date . '"');
        $result['total'] = $this->pdo->count_all_results('entrances as me');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('concat(month(me.created_at),"월") as entrance,count(*) as count', false);
        $this->pdo->join('users as u', 'me.user_id=u.id');
        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'u.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }
        $this->pdo->where('date(me.created_at) BETWEEN ADDDATE(LAST_DAY(date_add("' . $this->end_date . '",interval -3 month)), 1) AND "' . $this->end_date . '"');
        $this->pdo->group_by('CONCAT(YEAR(me.created_at), "/", MONTH(me.created_at))', false);
        $query = $this->pdo->get('entrances as me');
        $result['result'] = $query->result_array();

        return $result;
    }

    // 연령별
    private function age_count()
    {
        $result = array();

        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('product_relations as pr', 'pr.product_id=op.product_id');
        $this->pdo->join('users as u', 'o.user_id=u.id', 'left');

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'a.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }

        $this->pdo->where(array('pr.product_relation_type_id' => PRIMARY_COURSE_ID, 'o.re_order' => 0, 'a.account_category_id' => 1, 'o.enable' => 1, 'a.enable' => 1));
        $this->pdo->where('DATE(a.transaction_date) BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" AND (a.cash+a.credit)>0');
        $result['total'] = $this->pdo->count_all_results('enrolls as e');

        if (!$result['total']) {
            return $result;
        }
        $this->pdo->select('case
            when birthday is null then "생년월일 없음"
            when year(now())-year(birthday) > 48 then "50대"
            when year(now())-year(birthday) > 38 then "40대"
            when year(now())-year(birthday) > 28 then "30대"
            when year(now())-year(birthday) > 18 then "20대"
            else "10대 이하"
            end as age_group,count(*) as count', false);

        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('product_relations as pr', 'pr.product_id=op.product_id');
        $this->pdo->join('users as u', 'o.user_id=u.id', 'left');

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'u.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }

        $this->pdo->where(array('pr.product_relation_type_id' => PRIMARY_COURSE_ID, 'o.re_order' => 0, 'a.account_category_id' => 1, 'o.enable' => 1, 'a.enable' => 1));
        $this->pdo->where('DATE(a.transaction_date) BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" AND (a.cash+a.credit)>0');
        $this->pdo->group_by('age_group');
        $query = $this->pdo->get('enrolls as e');
        $result['result'] = $query->result_array();

        return $result;
    }

    private function gender_count()
    {
        $result = array();

        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('product_relations as pr', 'pr.product_id=op.product_id');
        $this->pdo->join('users as u', 'o.user_id=u.id', 'left');


        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'a.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }

        $this->pdo->where(array('pr.product_relation_type_id' => PRIMARY_COURSE_ID, 'o.re_order' => 0, 'a.account_category_id' => 1, 'o.enable' => 1, 'a.enable' => 1));
        $this->pdo->where('DATE(o.transaction_date) BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" AND (a.cash+a.credit)>0');
        $result['total'] = $this->pdo->count_all_results('enrolls as e');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('if(gender=1,"' . _('Male') . '",if(gender=0,"' . _('Female') . '","' . _('Not Insert Or Deleted') . '")) as gender,count(*) as count');
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('account_orders as ao', 'ao.order_id=o.id');
        $this->pdo->join('accounts as a', 'ao.account_id=a.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('product_relations as pr', 'pr.product_id=op.product_id');
        $this->pdo->join('users as u', 'o.user_id=u.id', 'left');

        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'a.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }

        $this->pdo->where(array('pr.product_relation_type_id' => PRIMARY_COURSE_ID, 'o.re_order' => 0, 'a.account_category_id' => 1, 'o.enable' => 1, 'a.enable' => 1));
        $this->pdo->where('DATE(o.transaction_date) BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '" AND (a.cash+a.credit)>0');
        $this->pdo->group_by('u.gender');
        $query = $this->pdo->get('enrolls as e');
        $result['result'] = $query->result_array();

        return $result;
    }

    private function entrance_week()
    {
        $result = array();

        $this->pdo->join('users as u', 'me.user_id=u.id');
        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'u.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }
        $this->pdo->where('date(me.created_at) BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '"');
        $this->pdo->where('(me.created_at is not null AND me.created_at!="0000-00-00 00:00:00")');
        $result['total'] = $this->pdo->count_all_results('entrances as me');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('DAYNAME(me.in_time) as entrance,count(*) as count', false);
        $this->pdo->join('users as u', 'me.user_id=u.id');
        if ($this->session->userdata('branch_id')) {
            $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id')));
        } else {
            $this->pdo->join('branches as b', 'u.branch_id=b.id');
            $this->pdo->where(array('b.center_id' => $this->session->userdata('center_id')));
        }
        $this->pdo->where('date(me.created_at) BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '"');
        $this->pdo->where('(me.created_at is not null AND me.created_at!="0000-00-00 00:00:00")');
        $this->pdo->group_by('entrance');
        $query = $this->pdo->get('entrances as me');
        $entrance_week = $query->result_array();

        $result['result'] = array(
            array('entrance' => 'Monday', 'count' => 0),
            array('entrance' => 'Tuesday', 'count' => 0),
            array('entrance' => 'Wednesday', 'count' => 0),
            array('entrance' => 'Thursday', 'count' => 0),
            array('entrance' => 'Friday', 'count' => 0),
            array('entrance' => 'Saturday', 'count' => 0),
            array('entrance' => 'Sunday', 'count' => 0),
        );

        // 데이터 있으면 기본값에 변경
        foreach ($entrance_week as $entrance_data) {
            switch ($entrance_data['entrance']) {
                case 'Monday':
                    $result['result'][0]['count'] = $entrance_data['count'];
                    break;
                case 'Tuesday':
                    $result['result'][1]['count'] = $entrance_data['count'];
                    break;
                case 'Wednesday':
                    $result['result'][2]['count'] = $entrance_data['count'];
                    break;
                case 'Thursday':
                    $result['result'][3]['count'] = $entrance_data['count'];
                    break;
                case 'Friday':
                    $result['result'][4]['count'] = $entrance_data['count'];
                    break;
                case 'Saturday':
                    $result['result'][5]['count'] = $entrance_data['count'];
                    break;
                case 'Sunday':
                    $result['result'][6]['count'] = $entrance_data['count'];
                    break;
            }
        }

        return $result;
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(array($this->table . '.id' => $id));
        }

        return $this->pdo->count_all_results($this->table);
    }
}
