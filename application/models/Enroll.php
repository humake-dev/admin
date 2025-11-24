<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'OrderExtend.php';

class Enroll extends OrderExtend
{
    protected $table = 'enrolls';
    protected $accepted_attributes = array('order_id', 'course_id', 'type', 'quantity', 'use_quantity', 'insert_quantity', 'start_date', 'end_date', 'have_datetime');
    protected $get_current_only = true;
    protected $get_end_only = false;
    protected $get_start_only = false;
    protected $get_not_end_only = false;

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select = 'e.*,oc.id as content_id,oc.content,od.total_dc_price,od.dc_rate,od.dc_price,o.user_id,o.original_price,o.price,o.payment,o.re_order,o.enable,o.stopped,o.created_at,o.updated_at,
        (SELECT a.transaction_date FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=e.order_id ORDER BY a.id LIMIT 1) as transaction_date,
        o.transaction_date as order_transaction_date,
        (SELECT count(*) FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=e.order_id AND a.type="O") as refund, 
        if(o.stopped=1,(SELECT os.stop_end_date FROM order_stops AS os WHERE os.order_id=o.id AND os.enable=1 ORDER BY os.id desc LIMIT 1),null) AS stop_end_date,
        if(o.stopped=1,(SELECT if(os.stop_day_count,DATE_ADD(e.start_date,INTERVAL os.stop_day_count Day),null) FROM order_stops AS os WHERE os.order_id=o.id AND os.enable=1 and os.is_change_start_date=1 ORDER BY os.id desc LIMIT 1),null) AS change_start_date,
        if(o.stopped=1,(SELECT if(os.stop_day_count,DATE_ADD(e.end_date,INTERVAL os.stop_day_count Day),null) FROM order_stops AS os WHERE os.order_id=o.id AND os.enable=1 ORDER BY os.id desc LIMIT 1),null) AS change_end_date,
        DATEDIFF(e.start_date,e.end_date) as day_count,
        (SELECT SUM(stop_day_count) FROM user_stops WHERE enable=1 AND order_id=o.id) as total_stop_day_count,
        pc.title as product_category_name,
        p.title as product_name,
        p.price as product_price,
        p.id as product_id,
        et.trainer_id,
        a.commission_rate,
        a.name as trainer_name,
        u.name as user_name,
        u.phone as phone,
        u.registration_date as registration_date,
        uc.card_no as card_no,
        c.lesson_dayofweek as dow,
        c.lesson_type,
        c.lesson_quantity,   
        c.lesson_period_unit,
        c.product_id,
        ec.commission, 
        ept.serial as pt_serial,
        IF(e.end_date<"2050-01-01",DATEDIFF(e.end_date,e.start_date),0) as total_date,        
        IF(e.end_date<"2050-01-01",DATEDIFF(e.end_date,curdate()),0) as left_date,
        IF(oe.id,1,0) as ended
        ';

        if (!empty($this->edit_log_count)) {
            $select .= ',(SELECT count(*) FROM order_edit_logs WHERE order_id=e.order_id) as edit_log_count';
        }

        $this->pdo->select($select);
        $this->pdo->join('orders as o', 'e.order_id = o.id');
        $this->pdo->join('order_contents as oc', 'oc.order_id = o.id', 'left');
        $this->pdo->join('order_discounts as od', 'od.order_id = o.id', 'left');
        $this->pdo->join('courses as c', 'e.course_id = c.id');
        $this->pdo->join('products as p', 'c.product_id = p.id');
        $this->pdo->join('product_categories as pc', 'p.product_category_id = pc.id');
        $this->pdo->join('users as u', 'o.user_id = u.id', 'left');
        $this->pdo->join('user_access_cards as uc', 'uc.user_id = u.id', 'left');
        $this->pdo->join('enroll_trainers as et', 'et.enroll_id = e.id', 'left');
        $this->pdo->join('enroll_commissions as ec', 'ec.enroll_id = e.id', 'left');
        $this->pdo->join('enroll_pts as ept', 'ept.enroll_id=e.id', 'left');
        $this->pdo->join('admins as a', 'et.trainer_id = a.id', 'left');
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');

        if (!empty($this->lesson_type)) {
            if (is_array($this->lesson_type)) {
                $this->pdo->where_in('c.lesson_type', $this->lesson_type);
            } else {
                $this->pdo->where(array('c.lesson_type' => $this->lesson_type));
            }
        }

        if (!empty($this->course_id)) {
            $this->pdo->where(array('e.course_id' => $this->course_id));
        }

        if (isset($this->product_id)) {
            if (is_array($this->product_id)) {
                $this->pdo->where_in('p.id', $this->product_id);
            } else {
                $this->pdo->where(array('p.id' => $this->product_id));
            }
        }

        if (isset($this->user_id)) {
            if (is_array($this->user_id)) {
                $this->pdo->where_in('o.user_id', $this->user_id);
            } else {
                $this->pdo->where(array('o.user_id' => $this->user_id));
            }
        }

        if (!empty($this->not_user_id)) {
            $this->pdo->where('o.user_id !=', $this->not_user_id);
        }

        if (!empty($this->order_id)) {
            $this->pdo->where(array('e.order_id' => $this->order_id));
        }

        if (!empty($this->not_order_id)) {
            $this->pdo->where('o.id !=', $this->not_order_id);
        }

        if (!empty($this->trainer_id)) {
            $this->pdo->where(array('et.trainer_id' => $this->trainer_id));
        }

        if (!empty($this->period_start_date) or !empty($this->period_end_date)) {
            if (!empty($this->period_end_date)) {
                $this->pdo->where('((c.lesson_type!=4 AND e.start_date <= "' . $this->period_end_date . '") OR (c.lesson_type=4 AND e.quantity>e.use_quantity) OR o.stopped=1)', null, false);
            }

            if (!empty($this->period_start_date)) {
                $this->pdo->where('((c.lesson_type!=4 AND e.end_date >= "' . $this->period_start_date . '") OR (c.lesson_type=4 AND e.quantity>e.use_quantity) OR o.stopped=1)', null, false);
            }
        } else {
            if ($this->get_end_only or $this->get_start_only) {
                if ($this->get_start_only) {
                    $this->pdo->where('e.start_date <= CURDATE() AND o.stopped=0 AND oe.id is NULL');
                }

                if ($this->get_end_only) {
                    $this->pdo->where('((((c.lesson_type!=4 AND e.end_date < CURDATE()) OR (c.lesson_type=4 AND e.quantity<=e.use_quantity)) AND o.stopped=0) OR oe.id is NOT NULL)');
                }
            } else {
                if ($this->get_not_end_only) {
                    $this->pdo->where('(((c.lesson_type!=4 AND e.end_date >= CURDATE()) OR (c.lesson_type=4 AND e.quantity>e.use_quantity) OR o.stopped=1) AND oe.id is NULL)');
                } else {
                    if ($this->get_current_only) {
                        $this->pdo->where('(((c.lesson_type!=4 AND e.start_date <= CURDATE() AND e.end_date >= CURDATE()) OR (c.lesson_type=4 AND e.quantity>e.use_quantity) OR o.stopped=1) AND oe.id is NULL)');
                    }
                }
            }
        }

        if (!empty($this->search_start_date)) {
            $this->pdo->where('o.transaction_date >=', $this->search_start_date, false);
        }

        if (!empty($this->search_end_date)) {
            $this->pdo->where('o.transaction_date <=', $this->search_end_date, false);
        }

        if (!empty($this->primary_only)) {
            $this->pdo->join('product_relations as pr', 'pr.product_id=p.id');
            $this->pdo->where(array('product_relation_type_id' => PRIMARY_COURSE_ID));
        }

        if (isset($this->stopped)) {
            if (empty($this->stopped)) {
                $this->pdo->where(array('o.stopped' => 0));
            } else {
                if ($this->stopped != 'all') {
                    $this->pdo->where(array('o.stopped' => 1));
                }
            }
        }

        if(empty($this->center_id)) {
            if (empty($this->branch_id)) {
                $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'o.enable' => 1));
            } else {
                $this->pdo->where(array('o.branch_id' => $this->branch_id, 'o.enable' => 1));
            }
        } else {
            $this->pdo->where(array('o.enable' => 1));
        }
        
        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as e', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (!empty($this->group_by_user)) {
            $this->pdo->select('count(*) as count');
        }

        $this->pdo->join('orders as o', 'e.order_id = o.id');
        $this->pdo->join('courses as c', 'e.course_id = c.id');
        $this->pdo->join('products as p', 'c.product_id = p.id');
        $this->pdo->join('product_categories as pc', 'p.product_category_id = pc.id');
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');

        if (isset($id)) {
            $this->pdo->where(array('e.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as e');
        }

        if (!empty($this->lesson_type)) {
            if (is_array($this->lesson_type)) {
                $this->pdo->where_in('c.lesson_type', $this->lesson_type);
            } else {
                $this->pdo->where(array('c.lesson_type' => $this->lesson_type));
            }
        }

        if (!empty($this->course_id)) {
            $this->pdo->where(array('e.course_id' => $this->course_id));
        }

        if (isset($this->product_id)) {
            if (is_array($this->product_id)) {
                $this->pdo->where_in('p.id', $this->product_id);
            } else {
                $this->pdo->where(array('p.id' => $this->product_id));
            }
        }

        if (isset($this->user_id)) {
            if (is_array($this->user_id)) {
                $this->pdo->where_in('o.user_id', $this->user_id);
            } else {
                $this->pdo->where(array('o.user_id' => $this->user_id));
            }
        }

        if (!empty($this->not_user_id)) {
            $this->pdo->where('o.user_id !=', $this->not_user_id);
        }

        if (!empty($this->order_id)) {
            $this->pdo->where(array('e.order_id' => $this->order_id));
        }

        if (!empty($this->not_order_id)) {
            $this->pdo->where('o.id !=', $this->not_order_id);
        }

        if (!empty($this->trainer_id)) {
            $this->pdo->join('enroll_trainers as et', 'et.enroll_id = e.id');
            $this->pdo->where(array('et.trainer_id' => $this->trainer_id));
        }
        
        if (!empty($this->period_start_date) or !empty($this->period_end_date)) {
            if (!empty($this->period_end_date)) {
                $this->pdo->where('((c.lesson_type!=4 AND e.start_date <= "' . $this->period_end_date . '") OR (c.lesson_type=4 AND e.quantity>e.use_quantity) OR o.stopped=1)', null, false);
            }

            if (!empty($this->period_start_date)) {
                $this->pdo->where('((c.lesson_type!=4 AND e.end_date >= "' . $this->period_start_date . '") OR (c.lesson_type=4 AND e.quantity>e.use_quantity) OR o.stopped=1)', null, false);
            }
        } else {
            if ($this->get_end_only or $this->get_start_only) {
                if ($this->get_start_only) {
                    $this->pdo->where('e.start_date <= CURDATE() AND o.stopped=0 AND oe.id is NULL');
                }

                if ($this->get_end_only) {
                    $this->pdo->where('((((c.lesson_type!=4 AND e.end_date < CURDATE()) OR (c.lesson_type=4 AND e.quantity<=e.use_quantity)) AND o.stopped=0) OR oe.id is NOT NULL)');
                }
            } else {
                if ($this->get_not_end_only) {
                    $this->pdo->where('(((c.lesson_type!=4 AND e.end_date >= CURDATE()) OR (c.lesson_type=4 AND e.quantity>e.use_quantity) OR o.stopped=1) AND oe.id is NULL)');
                } else {
                    if ($this->get_current_only) {
                        $this->pdo->where('(((c.lesson_type!=4 AND e.start_date <= CURDATE() AND e.end_date >= CURDATE()) OR (c.lesson_type=4 AND e.quantity>e.use_quantity) OR o.stopped=1) AND oe.id is NULL)');
                    }
                }
            }
        }

        if (!empty($this->search_start_date)) {
            $this->pdo->where('o.transaction_date >=', $this->search_start_date);
        }

        if (!empty($this->search_end_date)) {
            $this->pdo->where('o.transaction_date <=', $this->search_end_datese);
        }

        if (!empty($this->primary_only)) {
            $this->pdo->join('product_relations as pr', 'pr.product_id=p.id');
            $this->pdo->where(array('product_relation_type_id' => PRIMARY_COURSE_ID));
        }

        if (isset($this->stopped)) {
            if (empty($this->stopped)) {
                $this->pdo->where(array('o.stopped' => 0));
            } else {
                if ($this->stopped != 'all') {
                    $this->pdo->where(array('og.stopped' => 1));
                }
            }
        }
        
        if(empty($this->center_id)) {
            if (empty($this->branch_id)) {
                $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'o.enable' => 1));
            } else {
                $this->pdo->where(array('o.branch_id' => $this->branch_id, 'o.enable' => 1));
            }
        } else {
            $this->pdo->where(array('o.enable' => 1));
        }
        
        if (!empty($this->group_by_user)) {
            $this->pdo->group_by('o.user_id');
        }

        return $this->pdo->count_all_results($this->table . ' as e');
    }

    protected function get_content_data($id)
    {
        $select = 'e.*,p.id as product_id,pc.id as product_category_id,oc.id as content_id,oc.content,od.total_dc_price,od.dc_rate,od.dc_price,o.user_id,o.original_price,o.price,o.payment,o.stopped,o.re_order,o.enable,o.created_at,o.updated_at,
        (SELECT a.transaction_date FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=e.order_id AND a.enable=1 AND a.account_category_id!=' . ADD_COMMISSION . ' ORDER BY a.id ASC LIMIT 1) as transaction_date,
        (SELECT SUM(if(STRCMP(a.type,"I")>0,-(a.cash),a.cash)) FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=e.order_id AND a.enable=1 AND a.account_category_id!=' . ADD_COMMISSION . ' GROUP BY e.id) as cash,
        (SELECT SUM(if(STRCMP(a.type,"I")>0,-(a.credit),a.credit)) FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=e.order_id AND a.enable=1 AND a.account_category_id!=' . ADD_COMMISSION . ' GROUP BY e.id) as credit,
        pc.title as product_category_name,
        p.title as product_name,
        p.price as product_price,
        c.lesson_dayofweek,
        c.quota,
        c.lesson_type,
        c.lesson_quantity,
        c.lesson_period,     
        c.lesson_period_unit,
        et.trainer_id,
        u.name as user_name,
        u.phone as phone,
        u.registration_date as registration_date,
        a.name as trainer_name,
        uac.card_no,
        ec.commission,
        ept.serial as pt_serial,
        IF(e.end_date<"2050-01-01",DATEDIFF(e.end_date,e.start_date),0) as total_date,    
        IF(e.end_date<"2050-01-01",DATEDIFF(e.end_date,curdate()),0) as left_date,
        IF(oe.id,1,0) as ended
        ';

        $this->pdo->select($select);
        $this->pdo->join('orders as o', 'e.order_id = o.id');
        $this->pdo->join('courses as c', 'e.course_id = c.id');
        $this->pdo->join('products as p', 'c.product_id = p.id');
        $this->pdo->join('users as u', 'o.user_id=u.id', 'left');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');
        $this->pdo->join('order_contents as oc', 'oc.order_id = o.id', 'left');
        $this->pdo->join('order_discounts as od', 'od.order_id = o.id', 'left');
        $this->pdo->join('product_categories as pc', 'p.product_category_id=pc.id', 'left');
        $this->pdo->join('enroll_trainers as et', 'et.enroll_id = e.id', 'left');
        $this->pdo->join('enroll_commissions as ec', 'ec.enroll_id = e.id', 'left');
        $this->pdo->join('enroll_pts as ept', 'ept.enroll_id=e.id', 'left');
        $this->pdo->join('admins as a', 'et.trainer_id = a.id', 'left');
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');

        $this->pdo->where(array('e.id' => $id, 'o.enable' => 1));
        $query = $this->pdo->get($this->table . ' as e');

        return $query->row_array();
    }

    public function use_quantity(array $data)
    {
        $this->pdo->where('e.quantity>e.use_quantity');
        $this->pdo->where(array('e.id' => $data['id']));
        if (!$this->pdo->count_all_results($this->table . ' as e')) {
            return false;
        }

        return $this->pdo->update($this->table, array('use_quantity' => ($data['use_quantity'] + 1)), array('id' => $data['id']));
    }

    public function back_quantity(array $data)
    {
        $this->pdo->where(array('e.id' => $data['id']));
        if (!$this->pdo->count_all_results($this->table . ' as e')) {
            return false;
        }

        return $this->pdo->update($this->table, array('use_quantity' => ($data['use_quantity'] - 1)), array('id' => $data['id']));
    }

    public function get_relation_enroll($course_id, $user_id, $trainer_id = null)
    {
        $result = array();

        $this->pdo->join('orders as o', 'e.order_id = o.id');
        $this->pdo->join('courses as c', 'e.course_id = c.id');
        $this->pdo->join('order_ends as oe', 'oe.order_id = o.id', 'left');
        $this->pdo->where('(e.quantity>e.use_quantity+(select count(*) FROM reservation_users as ru INNER JOIN reservations as r ON ru.reservation_id=r.id WHERE enroll_id=e.id AND r.branch_id=o.branch_id AND complete in (0,1)))  AND oe.id IS NULL');
        $this->pdo->where(array('e.course_id' => $course_id, 'o.user_id' => $user_id, 'c.lesson_type' => 4, 'o.stopped' => 0, 'o.enable' => 1));
        $result['total'] = $this->pdo->count_all_results($this->table . ' as e');

        if (empty($result['total'])) {
            return false;
        }

        if (empty($trainer_id)) {
            $this->pdo->select('e.*,et.trainer_id');
        } else {
            $this->pdo->select("
            e.*,
            et.trainer_id,
            (CASE WHEN et.trainer_id = {$trainer_id} THEN 0 ELSE 1 END) AS t_order
        ", false);
        }

        $this->pdo->join('orders as o', 'e.order_id = o.id');
        $this->pdo->join('courses as c', 'e.course_id = c.id');
        $this->pdo->join('order_ends as oe', 'oe.order_id = o.id', 'left');
        $this->pdo->join('enroll_trainers as et', 'et.enroll_id = e.id', 'left');
        $this->pdo->where('(e.quantity>e.use_quantity+(select count(*) FROM reservation_users as ru INNER JOIN reservations as r ON ru.reservation_id=r.id WHERE enroll_id=e.id AND r.branch_id=o.branch_id AND complete in (0,1))) AND oe.id IS NULL');
        $this->pdo->where(array('e.course_id' => $course_id, 'o.user_id' => $user_id, 'c.lesson_type' => 4, 'o.stopped' => 0, 'o.enable' => 1));

        if (empty($trainer_id)) {
            $this->pdo->order_by('e.have_datetime asc,e.id asc');
        } else {
            $this->pdo->order_by('t_order asc,e.have_datetime asc,e.id asc' );
        }

        $query = $this->pdo->get($this->table . ' as e');
        $result = $query->row_array();
        return $result;
    }

    public function extend(array $data)
    {
        parent::extend($data);

        return $this->pdo->update($this->table, array('insert_quantity' => $data['insert_quantity'], 'end_date' => $data['end_date']), array('id' => $data['id']));
    }
}
