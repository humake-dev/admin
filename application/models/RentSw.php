<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'OrderExtend.php';

class RentSw extends OrderExtend
{
    protected $table = 'rent_sws';
    protected $accepted_attributes = array('order_id', 'start_date', 'end_date', 'insert_quantity');
    protected $get_current_only = false;
    protected $get_end_only = false;
    protected $get_start_only = false;
    protected $get_not_end_only = false;
    protected $search = false;
    protected $search_type = 'default';

    protected function set_search($count = false)
    {
        if (empty($count)) {
            $this->pdo->select('rsw.*,p.title as product_name,oc.id as content_id,oc.content,od.dc_rate,od.dc_price,o.user_id,o.original_price,o.price,o.payment,o.enable,o.stopped,o.created_at,o.updated_at,
            (SELECT a.transaction_date FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=o.id ORDER BY a.id LIMIT 1) as transaction_date,
            IF(o.stopped=1,(SELECT os.stop_end_date FROM order_stops AS os WHERE os.order_id=o.id AND os.enable=1 ORDER BY os.id desc LIMIT 1),null) AS stop_end_date,        
            IF(o.stopped=1,(SELECT IF(os.stop_day_count,DATE_ADD(rsw.end_date,INTERVAL os.stop_day_count Day),null) FROM order_stops AS os WHERE os.order_id=o.id AND os.enable=1 ORDER BY os.id desc LIMIT 1),null) AS change_end_date,        
            u.name AS user_name,
            IF(date(rsw.end_date)<"2050-01-01",DATEDIFF(date(rsw.end_date),date(rsw.start_date)),0) as total_date,
            IF(date(rsw.end_date)<"2050-01-01",DATEDIFF(date(rsw.end_date),curdate()),0) as left_date,
            IF(oe.id,1,0) as ended');
        }

        $this->pdo->join('orders AS o', 'rsw.order_id=o.id');
        $this->pdo->join('order_products AS op', 'op.order_id=o.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('products AS p', 'op.product_id=p.id');
        $this->pdo->join('order_discounts as od', 'od.order_id = o.id', 'left');
        $this->pdo->join('order_contents AS oc', 'oc.order_id=o.id', 'left');
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');
        $this->pdo->join('account_orders AS ao', 'ao.order_id=o.id', 'left');
        $this->pdo->join('accounts AS a', 'ao.account_id=a.id', 'left');

        if (isset($this->search)) {
            switch ($this->search_type) {
                case 'field':
                    if (!empty($this->search['search_field']) and !empty($this->search['search_word'])) {
                        $this->pdo->like('u.' . $this->search['search_field'], trim($this->search['search_word']));
                    }
                    break;
                default:
                    $payment_search = true;

                    if (empty($this->search['payment_id'])) {
                        $payment_search = false;
                    }

                    if (isset($this->search['user_type'])) {
                        if ($this->search['user_type'] == 'free') {
                            $payment_search = false;
                        }
                    }

                    if (!empty($payment_search)) {
                        if ($this->search['user_type'] == 'free') {
                            $payment_search = false;
                        }
                    }

                    if (!empty($payment_search)) {
                        switch ($this->search['payment_id']) {
                            case 'status1':
                                $this->pdo->where('a.cash>0');
                                break;
                            case 'status2':
                                $this->pdo->where('a.credit>0');
                                break;
                        }
                    }

                    if (isset($this->search['status_type'])) {
                        switch ($this->search['status_type']) {
                            case 'using':
                                $this->get_end_only = false;
                                $this->get_start_only = false;
                                $this->get_current_only = true;
                                break;
                            case 'expired':
                                $this->get_end_only = true;
                                $this->get_start_only = false;
                                $this->get_current_only = false;
                                break;
                            case 'reservation':
                                $this->get_end_only = false;
                                $this->get_start_only = false;
                                $this->get_current_only = false;
                                $this->pdo->where('(((rsw.end_date >= CURDATE() OR o.stopped=1) AND oe.id is NULL) AND (rsw.start_date > CURDATE()))');
                                break;
                            default :
                        }
                    }

                    if (isset($this->search['search_period'])) {
                        switch ($this->search['search_period']) {
                            case 'transaction_date':
                                $this->pdo->where('o.transaction_date>="' . $this->start_date . '" AND o.transaction_date<="' . $this->end_date . '"');
                                break;
                            case 'start_date':
                                $this->pdo->where('rsw.start_date>="' . $this->start_date . '" AND rsw.start_date<="' . $this->end_date . '"');
                                break;
                            case 'end_date':
                                $this->pdo->where('rsw.end_date>="' . $this->start_date . '" AND rsw.end_date<="' . $this->end_date . '"');
                                break;
                        }
                    }
            }
        }

        if (!empty($this->stopped)) {
            if (!empty($this->start_date)) {
                $this->pdo->where(array('rsw.start_date' => $this->start_date));
            }

            if (!empty($this->end_date)) {
                $this->pdo->where(array('rsw.end_date' => $this->end_date));
            }
        }
        
        if (empty($this->branch_id)) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'o.enable' => 1));
        } else {
            $this->pdo->where(array('o.branch_id' => $this->branch_id, 'o.enable' => 1));
        }

        if (isset($this->search['user_type'])) {
            switch ($this->search['user_type']) {
                case 'all':
                    break;
                case 'free':
                    $this->pdo->having('SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit)))<=', 0, false);
                    break;
                default:
                    $this->pdo->where('(a.enable=1 OR a.id is null) AND (a.account_category_id!=' . ADD_COMMISSION . ' OR a.id is null)');
                    $this->pdo->having('SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit)))>', 0, false);
            }
        }
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->set_search();

        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->display_only)) {
            $this->pdo->where('oe.id is null');
        }

        if (!empty($this->no_display_only)) {
            $this->pdo->where('oe.id is not null');
        }

        if (!empty($this->start_date)) {
            $this->pdo->where(array('rsw.start_date' => $this->start_date));
        }

        if (!empty($this->end_date)) {
            $this->pdo->where(array('rsw.end_date' => $this->end_date));
        }

        if (!empty($this->period_start_date) or !empty($this->period_end_date)) {
            if (!empty($this->period_end_date)) {
                $this->pdo->where('rsw.start_date <=', $this->period_end_date);
            }

            if (!empty($this->period_start_date)) {
                $this->pdo->where('rsw.end_date >=', $this->period_start_date);
            }
            $this->pdo->where('r.end_date >= CURDATE()');
        } else {
            if ($this->get_end_only or $this->get_start_only) {
                if ($this->get_start_only) {
                    $this->pdo->where('rsw.start_date <= CURDATE() AND o.stopped=0 AND oe.id is NULL');
                }

                if ($this->get_end_only) {
                    $this->pdo->where('((rsw.end_date <= CURDATE() AND o.stopped=0) OR oe.id is NOT NULL)');
                }
            } else {
                if ($this->get_current_only) {
                    $this->pdo->where('(((rsw.start_date <= CURDATE() AND rsw.end_date >= CURDATE()) OR o.stopped=1) AND oe.id is NULL)');
                }

                if ($this->get_not_end_only) {
                    $this->pdo->where('((rsw.end_date >= CURDATE() OR o.stopped=1) AND oe.id is NULL)');
                }
            }
        }

        if (!empty($this->product_id)) {
            $this->pdo->where(array('op.product_id' => $this->product_id));
        }

        $this->pdo->order_by($order, $desc);
        $this->pdo->group_by('o.id');
        $query = $this->pdo->get($this->table . ' as rsw', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*) as count');
        $this->set_search(true);

        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->display_only)) {
            $this->pdo->where('oe.id is null');
        }

        if (!empty($this->no_display_only)) {
            $this->pdo->where('oe.id is not null');
        }

        if (!empty($this->start_date)) {
            $this->pdo->where(array('rsw.start_date' => $this->start_date));
        }

        if (!empty($this->end_date)) {
            $this->pdo->where(array('rsw.end_date' => $this->end_date));
        }

        if (!empty($this->period_start_date) or !empty($this->period_end_date)) {
            if (!empty($this->period_end_date)) {
                $this->pdo->where('rsw.start_date <', $this->period_end_date);
            }

            if (!empty($this->period_start_date)) {
                $this->pdo->where('rsw.end_date >', $this->period_start_date);
            }
            $this->pdo->where('rsw.end_date >= CURDATE()');
        } else {
            if ($this->get_end_only or $this->get_start_only) {
                if ($this->get_start_only) {
                    $this->pdo->where('rsw.start_date <= CURDATE() AND o.stopped=0 AND oe.id is NULL');
                }

                if ($this->get_end_only) {
                    $this->pdo->where('((rsw.end_date <= CURDATE() AND o.stopped=0) OR oe.id is NOT NULL)');
                }
            } else {
                if ($this->get_current_only) {
                    $this->pdo->where('(((rsw.start_date <= CURDATE() AND rsw.end_date >= CURDATE()) OR o.stopped=1) AND oe.id is NULL)');
                }

                if ($this->get_not_end_only) {
                    $this->pdo->where('((rsw.end_date >= CURDATE() OR o.stopped=1) AND oe.id is NULL)');
                }
            }
        }

        if (!empty($this->product_id)) {
            $this->pdo->where(array('op.product_id' => $this->product_id));
        }

        if (isset($id)) {
            $this->pdo->where(array('rsw.id' => $id));
        }

        $this->pdo->group_by('o.id');
        return $this->pdo->count_all_results($this->table . ' as rsw');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('rsw.*,p.id as product_id,p.title as product_name,oc.id as content_id,oc.content,o.user_id,o.original_price,o.price,o.payment,
        (SELECT a.transaction_date FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=rsw.order_id ORDER BY a.id LIMIT 1) as transaction_date,
        rsw.start_date,
        rsw.end_date,
      u.name AS user_name,
      u.id AS user_id,
      uac.card_no,
      u.phone,
      IF(rsw.end_date<"2050-01-01",DATEDIFF(rsw.end_date,rsw.start_date),0) as total_date,
      IF(rsw.end_date<"2050-01-01",DATEDIFF(rsw.end_date,curdate()),0) as left_date,
      (SELECT SUM(a.cash) FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=o.id AND a.enable=1 GROUP BY ao.order_id) as cash,
      (SELECT SUM(a.credit) FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=o.id AND a.enable=1 GROUP BY ao.order_id) as credit,
      IF(oe.id,1,0) as ended, 
      ');

        $this->pdo->join('orders AS o', 'rsw.order_id=o.id', 'left');
        $this->pdo->join('order_products AS op', 'op.order_id=o.id');
        $this->pdo->join('order_contents AS oc', 'oc.order_id=o.id', 'left');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('products AS p', 'op.product_id=p.id');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');
        $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'rsw.id' => $id));
        $query = $this->pdo->get($this->table . ' as rsw');

        return $query->row_array();
    }
}
