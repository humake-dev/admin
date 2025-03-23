<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class TrainerActive extends SL_Model
{
    protected $table = 'enroll_use_logs';

    public function get_total()
    {
        $this->pdo->select('SUM(if(eul.id,1,0)) as total_period_use,SUM(ca.cash) as total_commission');
        $this->pdo->join('reservations as r', 'r.manager_id=trainer.id');
        $this->pdo->join('reservation_users as ru', 'ru.reservation_id=r.id');
        $this->pdo->join('enroll_use_logs as eul', 'eul.reservation_user_id=ru.id');
        $this->pdo->join('enrolls as e', 'ru.enroll_id=e.id');
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('accounts as ca', 'eul.account_id=ca.id');

        if (isset($this->start_date)) {
            $this->pdo->where('DATE(r.start_time) >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('DATE(r.end_time) <=', $this->end_date);
        }

        if (!empty($this->trainer_id)) {
            $this->pdo->where(array('trainer.id' => $this->trainer_id));
        }

        if (empty($this->szct)) {
            $this->pdo->where('ca.cash!=0');
        }
        
        $this->pdo->where(array('trainer.is_trainer' => 1, 'trainer.branch_id' => $this->session->userdata('branch_id'), 'trainer.enable' => 1, 'eul.enable' => 1));
        $this->pdo->order_by('trainer.name', 'asc');

        $query = $this->pdo->get('admins as trainer');

        return $query->row_array();
    }

    public function get_view_total($trainer_id)
    {
        $this->pdo->select('SUM(if(eul.id,1,0)) as total_period_use,SUM(ca.cash) as total_commission');
        $this->pdo->join('reservation_users as ru', 'ru.user_id=u.id');
        $this->pdo->join('reservations as r', 'ru.reservation_id=r.id');
        $this->pdo->join('enroll_use_logs as eul', 'eul.reservation_user_id=ru.id');
        $this->pdo->join('enrolls as e', 'ru.enroll_id=e.id');
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('accounts as ca', 'eul.account_id=ca.id');

        if (isset($this->start_date)) {
            $this->pdo->where('DATE(r.start_time) >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('DATE(r.end_time) <=', $this->end_date);
        }

        if (!empty($this->trainer_id)) {
            $this->pdo->where(array('r.manager_id' => $this->trainer_id));
        }

        if (empty($this->szct)) {
            $this->pdo->where('ca.cash!=0');
        }
        
        $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1, 'r.manager_id' => $trainer_id, 'eul.enable' => 1));
        $this->pdo->order_by('u.id', 'desc');

        $query = $this->pdo->get('users as u');

        return $query->row_array();
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select = 'trainer.name as name,trainer.id as employee_id,count(distinct u.id) as execute_user,count(distinct o.id) as count_order, count(distinct eul.id) as period_use,
        (select sum(enrolls.quantity) FROM enrolls INNER JOIN enroll_trainers ON enroll_trainers.enroll_id=enrolls.id  WHERE enroll_trainers.trainer_id=trainer.id) as quantity,
        (select sum(enrolls.use_quantity) FROM enrolls INNER JOIN enroll_trainers ON enroll_trainers.enroll_id=enrolls.id WHERE enroll_trainers.trainer_id=trainer.id) as use_quantity,
        (select COUNT(*) FROM users INNER JOIN user_trainers AS ut ON ut.user_id=users.id WHERE ut.trainer_id=trainer.id) as charge_user';

        if (isset($this->start_date) or isset($this->end_date)) {
            $select .= ',(select SUM(ca.cash) FROM enroll_use_logs INNER JOIN reservation_users ON enroll_use_logs.reservation_user_id=reservation_users.id INNER JOIN reservations ON reservation_users.reservation_id=reservations.id INNER JOIN accounts as ca ON enroll_use_logs.account_id=ca.id INNER JOIN account_commissions as ac ON ac.account_id=ca.id WHERE ac.employee_id=r.manager_id AND enroll_use_logs.enable=1';

            if (isset($this->start_date)) {
                $select .= ' and DATE(reservations.start_time) >="' . $this->start_date . '"';
            }

            if (isset($this->end_date)) {
                $select .= ' and DATE(reservations.start_time) <="' . $this->end_date . '"';
            }

            $select .= ') as commission';
        }

        $this->pdo->select($select);
        $this->pdo->join('reservations as r', 'r.manager_id=trainer.id');
        $this->pdo->join('reservation_users as ru', 'ru.reservation_id=r.id');
        $this->pdo->join('enroll_use_logs as eul', 'eul.reservation_user_id=ru.id');
        $this->pdo->join('enrolls as e', 'ru.enroll_id=e.id');
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('accounts as ca', 'eul.account_id=ca.id');

        if (isset($this->start_date)) {
            $this->pdo->where('DATE(r.start_time) >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('DATE(r.end_time) <=', $this->end_date);
        }

        if (!empty($this->trainer_id)) {
            $this->pdo->where(array('trainer.id' => $this->trainer_id));
        }

        if (empty($this->szct)) {
            $this->pdo->where('ca.cash!=0');
        }
        
        $this->pdo->where(array('trainer.is_trainer' => 1, 'trainer.branch_id' => $this->session->userdata('branch_id'), 'trainer.enable' => 1, 'eul.enable' => 1));
        $this->pdo->group_by('trainer.id');
        $this->pdo->order_by('trainer.name', 'asc');

        $query = $this->pdo->get('admins as trainer', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*)');
        $this->pdo->join('reservations as r', 'r.manager_id=trainer.id');
        $this->pdo->join('reservation_users as ru', 'ru.reservation_id=r.id');
        $this->pdo->join('enroll_use_logs as eul', 'eul.reservation_user_id=ru.id');
        $this->pdo->join('enrolls as e', 'ru.enroll_id=e.id');
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('accounts as ca', 'eul.account_id=ca.id');

        if (isset($this->start_date)) {
            $this->pdo->where('DATE(r.start_time) >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('DATE(r.end_time) <=', $this->end_date);
        }

        if (!empty($this->trainer_id)) {
            $this->pdo->where(array('trainer.id' => $this->trainer_id));
        }

        if (isset($id)) {
            $this->pdo->where(array('trainer.id' => $id));
        }

        if (empty($this->szct)) {
            $this->pdo->where('ca.cash!=0');
        }
        
        $this->pdo->where(array('trainer.is_trainer' => 1, 'trainer.branch_id' => $this->session->userdata('branch_id'), 'trainer.enable' => 1, 'eul.enable' => 1));
        $this->pdo->group_by('trainer.id');

        return $this->pdo->count_all_results('admins as trainer');
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
        $select = 'ufc.fc_id,fc.name as fc_name,DATE(MAX(r.start_time)) as current_execute_date,
        p.title as product_name,
        pc.title as product_category,
        o.price,
        e.id as enroll_id,
        e.start_date,
        e.end_date,
        e.insert_quantity,
        trainer.commission_rate,
        ec.commission as default_commission,
        count(distinct eul.id) as execute_count,
        u.name,u.id as user_id,uac.card_no';

        $select .= ',(select SUM(if(accounts.type="I",(accounts.cash+accounts.credit),-(accounts.cash+accounts.credit))) FROM orders INNER JOIN account_orders ON account_orders.order_id=orders.id INNER JOIN accounts ON account_orders.account_id=accounts.id WHERE orders.id=o.id and orders.enable=1 and accounts.enable=1 AND accounts.account_category_id!=25) as purchase';

        if (isset($this->start_date) or isset($this->end_date)) {
            $select .= ',(select SUM(ca.cash) FROM enroll_use_logs INNER JOIN reservation_users ON enroll_use_logs.reservation_user_id=reservation_users.id INNER JOIN reservations ON reservation_users.reservation_id=reservations.id INNER JOIN accounts as ca ON enroll_use_logs.account_id=ca.id INNER JOIN account_commissions as ac ON ac.account_id=ca.id WHERE ac.employee_id=r.manager_id and ca.user_id=u.id AND enroll_use_logs.enroll_id=e.id AND enroll_use_logs.enable=1';
            if (isset($this->start_date)) {
                $select .= ' and DATE(reservations.start_time) >="' . $this->start_date . '"';
            }

            if (isset($this->end_date)) {
                $select .= ' and DATE(reservations.end_time) <="' . $this->end_date . '"';
            }

            $select .= ') as commission';
        }

        $this->pdo->select($select, false);
        $this->pdo->join('reservation_users as ru', 'ru.user_id=u.id');
        $this->pdo->join('reservations as r', 'ru.reservation_id=r.id');
        $this->pdo->join('enroll_use_logs as eul', 'eul.reservation_user_id=ru.id');
        $this->pdo->join('enrolls as e', 'eul.enroll_id=e.id');
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('accounts as ca', 'eul.account_id=ca.id');
        $this->pdo->join('courses as c', 'e.course_id=c.id');
        $this->pdo->join('enroll_commissions as ec', 'ec.enroll_id=e.id', 'left');
        $this->pdo->join('products as p', 'c.product_id=p.id', 'left');
        $this->pdo->join('product_categories as pc', 'p.product_category_id=pc.id', 'left');
        $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id', 'left');
        $this->pdo->join('admins AS fc', 'ufc.fc_id=fc.id', 'left');
        $this->pdo->join('admins AS trainer', 'r.manager_id=trainer.id', 'left');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id = u.id', 'left');

        if (isset($this->start_date)) {
            $this->pdo->where('DATE(r.start_time) >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('DATE(r.end_time) <=', $this->end_date);
        }

        if (!empty($this->trainer_id)) {
            $this->pdo->where(array('r.manager_id' => $this->trainer_id));
        }

        if (empty($this->szct)) {
            $this->pdo->where('ca.cash!=0');
        }

        $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1, 'r.manager_id' => $id, 'eul.enable' => 1));

        $this->pdo->group_by('o.id,u.id');
        $this->pdo->order_by('u.name', 'asc');

        $query = $this->pdo->get('users as u', $per_page, $page);

        return $query->result_array();
    }

    public function get_view_count($id)
    {
        $this->pdo->select('count(*)');
        $this->pdo->join('reservation_users as ru', 'ru.user_id=u.id');
        $this->pdo->join('reservations as r', 'ru.reservation_id=r.id');
        $this->pdo->join('enroll_use_logs as eul', 'eul.reservation_user_id=ru.id');
        $this->pdo->join('enrolls as e', 'eul.enroll_id=e.id');
        $this->pdo->join('orders as o', 'e.order_id=o.id');
        $this->pdo->join('courses as c', 'e.course_id=c.id');
        $this->pdo->join('accounts as ca', 'eul.account_id=ca.id');

        if (isset($this->start_date)) {
            $this->pdo->where('DATE(r.start_time) >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('DATE(r.end_time) <=', $this->end_date);
        }

        if (!empty($this->trainer_id)) {
            $this->pdo->where(array('r.manager_id' => $this->trainer_id));
        }

        if (empty($this->szct)) {
            $this->pdo->where('ca.cash!=0');
        }
        
        $this->pdo->where(array('u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1, 'r.manager_id' => $id, 'eul.enable' => 1));

        $this->pdo->group_by('o.id,u.id');
        $result = $this->pdo->count_all_results('users as u');

        return $result;
    }
}
