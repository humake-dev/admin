<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class SearchYs extends SL_Model
{
    protected $table = 'users';
    protected $search_type = 'default';
    protected $search_pt = false;

    protected function set_search($count = false)
    {
        if (empty($count)) {
            $select = 'u.*,e.quantity as quantity,e.use_quantity as use_quantity,if(p.id,GROUP_CONCAT(distinct(p.title)),null) as product_name,
            (SELECT SUM(if(accounts.type="I",(accounts.cash+accounts.credit),-(accounts.cash+accounts.credit))) FROM accounts INNER JOIN account_orders ON accounts.id=account_orders.account_id WHERE accounts.enable=1 AND accounts.enable=1 AND accounts.account_category_id!=' . ADD_COMMISSION . ' AND account_orders.order_id=max(o.id))as pay_total,
            if(ot.id,ot.origin_quantity,e.insert_quantity) as insert_quantity,
            ot.id as ot_id,ot.origin_start_date,ot.origin_end_date,
            if(ot.id,if(ots.id,schedule_date,date(ot.created_at)),null) as transfer_date,
            e.start_date,e.end_date,
            if(o.stopped=1,(SELECT if(os.stop_day_count,DATE_ADD(MAX(e.start_date),INTERVAL os.stop_day_count Day),null) FROM order_stops WHERE order_stops.order_id=o.id AND order_stops.enable=1 and order_stops.is_change_start_date=1 ORDER BY order_stops.id desc LIMIT 1),null) AS change_start_date,
            if(o.stopped=1,(SELECT if(os.stop_day_count,DATE_ADD(MAX(e.end_date),INTERVAL os.stop_day_count Day),null) FROM order_stops WHERE order_stops.order_id=o.id AND order_stops.enable=1 ORDER BY order_stops.id desc LIMIT 1),null) AS change_end_date,
            (SELECT count(*) FROM product_relations WHERE product_relations.enable=1 AND product_relations.product_relation_type_id=4 AND product_relations.product_id=p.id) as is_primary,
            c.lesson_type,c.lesson_quantity,
            o.transaction_date';

            if ($this->search_pt) {
                $select .= (',(SELECT count(*) FROM enroll_use_logs INNER JOIN reservation_users ON enroll_use_logs.reservation_user_id=reservation_users.id INNER JOIN reservations ON reservation_users.reservation_id=reservations.id WHERE enroll_use_logs.enroll_id=e.id AND DATE(reservations.start_time)<="' . $this->reference_date . '") as pt_use_quantity');
            }

            $this->pdo->select($select);
        }

        $this->pdo->join('orders as o', 'o.user_id=u.id');
        $this->pdo->join('enrolls as e', 'e.order_id=o.id');
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');
        $this->pdo->join('account_orders AS ao', 'ao.order_id=o.id', 'left');
        $this->pdo->join('accounts AS a', 'ao.account_id=a.id', 'left');
        $this->pdo->join('account_products as op', 'op.account_id=a.id', 'left');
        $this->pdo->join('products as p', 'op.product_id=p.id', 'left');
        $this->pdo->join('courses as c', 'c.product_id=p.id', 'left');
        $this->pdo->join('order_transfers AS ot', 'ot.order_id=o.id', 'left');
        $this->pdo->join('order_transfer_schedules AS ots', 'ots.order_transfer_id=ot.id', 'left');
        $this->pdo->join('order_stops AS os', 'os.id = (SELECT max(id) FROM order_stops AS os2 WHERE os2.order_id = o.id)', 'left');

        if (!empty($this->fc_id) or !empty($this->trainer_id)) {
            if (!empty($this->fc_id) and !empty($this->trainer_id)) {
                if (empty($this->search_pt)) {
                    $this->pdo->where('(ut.trainer_id=' . $this->trainer_id . ' AND ufc.fc_id=' . $this->fc_id . ')', null, false);
                } else {
                    $this->pdo->where('(et.trainer_id=' . $this->trainer_id . ' AND ufc.fc_id=' . $this->fc_id . ')', null, false);
                }
            } else {
                if (!empty($this->trainer_id)) {
                    if (empty($this->search_pt)) {
                        $this->pdo->where('(ut.trainer_id=' . $this->trainer_id . ')', null, false);
                    } else {
                        $this->pdo->where('(et.trainer_id=' . $this->trainer_id . ')', null, false);
                    }
                }

                if (!empty($this->fc_id)) {
                    $this->pdo->where(['ufc.fc_id' => $this->fc_id]);
                }
            }
        }

        if (isset($this->product_id)) {
            if (is_array($this->product_id)) {
                $product_ids = $this->product_id;
                foreach ($product_ids as $p_index => $p_product_id) {
                    if (empty($p_product_id)) {
                        unset($product_ids[$p_index]);
                        continue;
                    }
                }

                $product_id = null;
                if (!empty(count($product_ids))) {
                    $product_id = $product_ids;
                }
            } else {
                $product_id = $this->product_id;
            }

            if (!empty($product_id)) {
                $product_id_exists = true;
                if (is_array($product_id)) {
                    if (count($product_id)) {
                        $this->pdo->where_in('op.product_id', $product_id);
                    } else {
                        $product_id_exists = false;
                    }
                } else {
                    $this->pdo->where(['op.product_id' => $product_id]);
                }

                if ($product_id_exists) {
                    $this->pdo->where('(a.enable=1 OR a.id is null) AND (a.account_category_id!=' . ADD_COMMISSION . ' OR a.id is null) AND (o.enable!=0)');
                }
            }
        }

        if ($this->search_pt) {
            $this->pdo->where(array('c.lesson_type' => 4));
        } else {
            $this->pdo->where('c.lesson_type!=4');
        }

        $this->pdo->where('o.transaction_date is not null AND oe.id is null AND o.enable=1 AND (a.cash+a.credit)>0 AND (ots.schedule_date=1 OR ots.schedule_date is null)');

        if (!empty($this->start_date)) {
            $this->pdo->where('o.transaction_date>="' . $this->start_date . '"');
        }

        if (!empty($this->end_date)) {
            $this->pdo->where('o.transaction_date<="' . $this->end_date . '"');
        }

        $this->pdo->where(['u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1, 'o.enable' => 1]);

        if (isset($this->search['user_type'])) {
            switch ($this->search['user_type']) {
                case 'all':
                    break;
                case 'free':
                    $this->pdo->having('if(SUM(if(c.lesson_type=4,1,0))>0,(CAST(SUM(e.quantity) AS SIGNED)-CAST(SUM(e.use_quantity) AS SIGNED)<=0),(SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit)))<=0))');
                    break;
                default:
                    $this->pdo->where('(a.enable=1 OR a.id is null) AND (a.account_category_id!=' . ADD_COMMISSION . ' OR a.id is null)');
                    $this->pdo->having('if(SUM(if(c.lesson_type=4,1,0))>0,(CAST(SUM(e.quantity) AS SIGNED)-CAST(SUM(e.use_quantity) AS SIGNED)>0),(SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit)))>0))');
            }
        }
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->set_search();
        $this->pdo->group_by('o.id');
        $this->pdo->order_by('u.id', $desc);
        $query = $this->pdo->get($this->table . ' as u', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*) as count');

        if (isset($id)) {
            $this->pdo->where([$this->table . '.id' => $id]);

            return $this->pdo->count_all_results($this->table . ' as u');
        }

        $this->set_search(true);
        $this->pdo->group_by('o.id');

        return $this->pdo->count_all_results($this->table . ' as u');
    }
}
