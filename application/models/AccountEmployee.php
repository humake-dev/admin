<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Account.php';

class AccountEmployee extends Account
{
    protected $type = 'trainer';

    public function get_total($employee_id)
    {
        $select = 'SUM(if(a.type="I",(a.cash+a.credit),0)) as total_income,SUM(if(a.type="O",(a.cash+a.credit),0)) as total_refund';

        if ($this->type == 'trainer') {
            $select .= ',(select sum(enrolls.quantity) FROM enrolls INNER JOIN enroll_trainers ON enroll_trainers.enroll_id=enrolls.id  WHERE enroll_trainers.trainer_id=et.trainer_id) as total_quantity,
            (select sum(enrolls.use_quantity) FROM enrolls INNER JOIN enroll_trainers ON enroll_trainers.enroll_id=enrolls.id WHERE enroll_trainers.trainer_id=et.trainer_id) as total_use';

            if (isset($this->start_date) or isset($this->end_date)) {
                $select .= ',SUM((select SUM(ca.cash) FROM enroll_use_logs INNER JOIN reservation_users ON enroll_use_logs.reservation_user_id=reservation_users.id INNER JOIN reservations ON reservation_users.reservation_id=reservations.id INNER JOIN accounts as ca ON enroll_use_logs.account_id=ca.id INNER JOIN account_commissions as ac ON ac.account_id=ca.id WHERE ac.employee_id=et.trainer_id and enroll_use_logs.enroll_id=e.id';
                if (isset($this->start_date)) {
                    $select .= ' and DATE(reservations.start_time) >="' . $this->start_date . '"';
                }

                if (isset($this->end_date)) {
                    $select .= ' and DATE(reservations.end_time) <="' . $this->end_date . '"';
                }

                $select .= ')) as total_commission';
            }
        }

        $this->pdo->select($select);
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id');
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id','left');
        $this->pdo->join('orders as o', 'ao.order_id=o.id','left');

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        if ($this->type == 'trainer') {
            $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
            $this->pdo->join('enroll_trainers as et', 'et.enroll_id=e.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id', 'left');
            $this->pdo->where(array('et.trainer_id' => $employee_id));
            $this->pdo->where(array('c.lesson_type' => 4));
        } else {
            $this->pdo->join('users as u', 'a.user_id=u.id');
            $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id');
            $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
            $this->pdo->join('courses as c', 'e.course_id=c.id', 'left');
            $this->pdo->where(array('ufc.fc_id' => $employee_id));
            $this->pdo->where('(a.account_category_id IN('.TRANSFER_ENROLL.','.TRANSFER_RENT.','.TRANSFER_ORDER.') OR (c.lesson_type!=4 OR c.lesson_type is null))');
        }

        $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));

        $query = $this->pdo->get($this->table . ' as a');

        return $query->row_array();
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select = 'a.*,ac.title as category_name,SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit))) as total_sales,COUNT(distinct u.id) as total_user';

        if ($this->type == 'trainer') {
            $select .= ',trainer.name as name,sum(e.quantity) as quantity,sum(e.use_quantity) as use_quantity,et.trainer_id as employee_id';
        } else {
            $select .= ',fc.name as name,COUNT(distinct u.id) as total_user,SUM(if(a.type="I",(a.cash+a.credit),0)) as total_income,SUM(if(a.type="O",(a.cash+a.credit),0)) as total_refund,ufc.fc_id as employee_id';
        }
        $this->pdo->select($select, false);
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id');
        $this->pdo->join('users as u', 'a.user_id=u.id', 'left');

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        if (isset($this->no_commission)) {
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION));
        }

        if (isset($this->no_branch_transfer)) {
            $this->pdo->where_not_in('a.account_category_id', array(BRANCH_TRANSFER));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));

        if ($this->type == 'trainer') {
            $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
            $this->pdo->join('enrolls as e', 'ao.order_id=e.order_id');
            $this->pdo->join('enroll_trainers as et', 'et.enroll_id=e.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id');
            $this->pdo->join('products as pr', 'c.product_id=pr.id', 'left');
            $this->pdo->join('product_categories as cc', 'pr.product_category_id=cc.id', 'left');
            $this->pdo->join('admins as trainer', 'et.trainer_id=trainer.id');

            if (isset($this->employee_id)) {
                $this->pdo->where(array('et.trainer_id' => $this->employee_id));
            }

            $this->pdo->where(array('c.lesson_type' => 4));
            $this->pdo->where(array('trainer.branch_id'=>$this->session->userdata('branch_id')));
            $this->pdo->group_by('et.trainer_id');
        } else {
            $this->pdo->join('account_orders as ao', 'ao.account_id=a.id','left');
            $this->pdo->join('enrolls as e', 'ao.order_id=e.order_id', 'left');
            $this->pdo->join('courses as c', 'e.course_id=c.id', 'left');
            $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id');
            $this->pdo->join('admins AS fc', 'ufc.fc_id=fc.id');

            if (isset($this->employee_id)) {
                $this->pdo->where(array('ufc.fc_id' => $this->employee_id));
            }
            $this->pdo->where('(a.account_category_id IN('.TRANSFER_ENROLL.','.TRANSFER_RENT.','.TRANSFER_ORDER.') OR (c.lesson_type!=4 OR c.lesson_type is null))');
            $this->pdo->where(array('fc.branch_id'=>$this->session->userdata('branch_id')));
            $this->pdo->group_by('ufc.fc_id');
        }

        $this->pdo->order_by('a.' . $order, $desc);

        $query = $this->pdo->get($this->table . ' as a', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*)');
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id');
        $this->pdo->join('users as u', 'a.user_id=u.id', 'left');

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        if (isset($id)) {
            $this->pdo->where(array($this->table . '.id' => $id));
        }

        if (isset($this->no_commission)) {
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION));
        }

        if (isset($this->no_branch_transfer)) {
            $this->pdo->where_not_in('a.account_category_id', array(BRANCH_TRANSFER));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));

        if ($this->type == 'trainer') {
            $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
            $this->pdo->join('enrolls as e', 'ao.order_id=e.order_id');
            $this->pdo->join('enroll_trainers as et', 'et.enroll_id=e.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id');
            $this->pdo->join('products as pr', 'c.product_id=pr.id', 'left');
            $this->pdo->join('product_categories as cc', 'pr.product_category_id=cc.id', 'left');
            $this->pdo->join('admins as trainer', 'et.trainer_id=trainer.id');

            if (isset($this->employee_id)) {
                $this->pdo->where(array('et.trainer_id' => $this->employee_id));
            }

            $this->pdo->where(array('c.lesson_type' => 4));
            $this->pdo->group_by('et.trainer_id');
        } else {
            $this->pdo->join('account_orders as ao', 'ao.account_id=a.id','left');
            $this->pdo->join('enrolls as e', 'ao.order_id=e.order_id', 'left');
            $this->pdo->join('courses as c', 'e.course_id=c.id', 'left');
            $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id');
            $this->pdo->join('admins AS fc', 'ufc.fc_id=fc.id');

            if (isset($this->employee_id)) {
                $this->pdo->where(array('ufc.fc_id' => $this->employee_id));
            }
            $this->pdo->where('(a.account_category_id IN('.TRANSFER_ENROLL.','.TRANSFER_RENT.','.TRANSFER_ORDER.') OR (c.lesson_type!=4 OR c.lesson_type is null))');
            $this->pdo->group_by('ufc.fc_id');
        }

        return $this->pdo->count_all_results($this->table . ' as a');
    }

    public function get_view_index($id, $per_page = 0, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $list = array();
        $list['total'] = $this->get_view_count($id);

        if (!$list['total']) {
            return $list;
        }

        $list['list'] = $this->get_view_data($id, $per_page, $page, $order, $desc, $enable);

        return $list;
    }

    protected function get_view_data($id, $per_page = 0, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select = 'a.transaction_date,uac.card_no,ufc.fc_id,fc.name as fc_name,od.dc_rate,od.dc_price,IF(pc.id,CONCAT(pc.title,"/",p.title),p.title) as product_name,
        SUM(if(STRCMP(a.type,"I")>0,-(a.cash+a.credit),(a.cash+a.credit))) as payment,
        p.id as product_id,
        if(e.start_date,e.start_date,if(r.id,if(r.start_datetime,date(r.start_datetime),""),"")) as start_date,
        if(e.end_date,e.end_date,if(r.id,if(r.end_datetime,date(r.end_datetime),""),"")) as end_date,
        u.name,a.*';

        $this->pdo->select($select, false);
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
        $this->pdo->join('account_products as ap', 'ap.account_id=a.id');
        $this->pdo->join('orders as o', 'ao.order_id=o.id');
        $this->pdo->join('users as u', 'a.user_id=u.id', 'left');
        $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id', 'left');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');
        $this->pdo->join('admins as fc', 'ufc.fc_id=fc.id', 'left');
        $this->pdo->join('order_discounts as od', 'od.order_id=o.id', 'left');
        $this->pdo->join('products as p', 'ap.product_id=p.id', 'left');
        $this->pdo->join('product_categories as pc', 'p.product_category_id=pc.id', 'left');
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
        $this->pdo->join('others as oo', 'oo.order_id=o.id', 'left');

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        if (isset($this->no_commission)) {
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION));
        }

        if (isset($this->no_branch_transfer)) {
            $this->pdo->where_not_in('a.account_category_id', array(BRANCH_TRANSFER));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));

        if ($this->type == 'trainer') {
            $this->pdo->join('enrolls as e', 'ao.order_id=e.order_id');
            $this->pdo->join('enroll_trainers as et', 'et.enroll_id=e.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id');
            $this->pdo->where(array('et.trainer_id' => $id));
            $this->pdo->where(array('c.lesson_type' => 4));
        } else {
            $this->pdo->join('enrolls as e', 'ao.order_id=e.order_id', 'left');
            $this->pdo->join('courses as c', 'e.course_id=c.id', 'left');
            $this->pdo->where('(a.account_category_id IN('.TRANSFER_ENROLL.','.TRANSFER_RENT.','.TRANSFER_ORDER.') OR (c.lesson_type!=4 OR c.lesson_type is null))');
            $this->pdo->where(array('ufc.fc_id' => $id));
        }

        $this->pdo->order_by('a.id', 'desc');
        $this->pdo->group_by('ao.order_id');

        $query = $this->pdo->get($this->table . ' as a', $per_page, $page);

        return $query->result_array();
    }

    public function get_view_count($id)
    {
        $this->pdo->select('count(*)');
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        if (isset($this->no_commission)) {
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION));
        }

        if (isset($this->no_branch_transfer)) {
            $this->pdo->where_not_in('a.account_category_id', array(BRANCH_TRANSFER));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));

        if ($this->type == 'trainer') {
            $this->pdo->join('enrolls as e', 'ao.order_id=e.order_id');
            $this->pdo->join('enroll_trainers as et', 'et.enroll_id=e.id');
            $this->pdo->join('courses as c', 'e.course_id=c.id');
            $this->pdo->where(array('et.trainer_id' => $id));
            $this->pdo->where(array('c.lesson_type' => 4));
        } else {
            $this->pdo->join('enrolls as e', 'ao.order_id=e.order_id', 'left');
            $this->pdo->join('courses as c', 'e.course_id=c.id', 'left');
            $this->pdo->join('users as u', 'a.user_id=u.id', 'left');
            $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id', 'left');
            $this->pdo->where('(a.account_category_id IN('.TRANSFER_ENROLL.','.TRANSFER_RENT.','.TRANSFER_ORDER.') OR (c.lesson_type!=4 OR c.lesson_type is null))');
            $this->pdo->where(array('ufc.fc_id' => $id));
        }

        $this->pdo->group_by('ao.order_id');
        $result = $this->pdo->count_all_results($this->table . ' as a');

        return $result;
    }
}
