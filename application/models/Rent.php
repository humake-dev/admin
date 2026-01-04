<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'OrderExtend.php';

class Rent extends OrderExtend
{
    protected $table = 'rents';
    protected $accepted_attributes = array('order_id', 'facility_id', 'no', 'insert_quantity', 'start_datetime', 'end_datetime');
    protected $get_current_only = false;
    protected $get_end_only = false;
    protected $get_start_only = false;
    protected $get_not_end_only = false;

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('r.*,p.id as product_id,p.title as product_name,oc.id as content_id,oc.content,o.user_id,o.original_price,o.price,o.payment,o.re_order,o.stopped,
        if(o.payment=0,o.transaction_date,(SELECT a.transaction_date FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=r.order_id ORDER BY a.id LIMIT 1)) as transaction_date,
        if(o.stopped=1,(SELECT if(os.stop_day_count,DATE_ADD(date(r.end_datetime),INTERVAL os.stop_day_count Day),null) FROM order_stops AS os WHERE os.order_id=o.id AND os.enable=1 ORDER BY os.id desc LIMIT 1),null) AS change_end_date,
        if(o.stopped=1,(SELECT os.stop_end_date FROM order_stops AS os WHERE os.order_id=o.id AND os.enable=1 ORDER BY os.id desc LIMIT 1),null) AS stop_end_date,        
        r.start_datetime as start_datetime,
        r.end_datetime as end_datetime,
        date(r.start_datetime) as start_date,
        date(r.end_datetime) as end_date,
        u.name AS user_name,
        fc.name as fc_name,
        uac.card_no,
        u.phone,  
        IF(r.end_datetime < NOW(),1,0) AS expired,
        IF(DATE(r.end_datetime)<"2050-01-01",DATEDIFF(DATE(r.end_datetime),DATE(r.start_datetime)),0) as total_date,
        IF(DATE(r.end_datetime)<"2050-01-01",DATEDIFF(DATE(r.end_datetime),CURDATE()),0) as left_date,
        IF(oe.id,1,0) as ended'
        );
        $this->pdo->join('orders as o', 'r.order_id = o.id');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('facilities AS f', 'r.facility_id=f.id');
        $this->pdo->join('products AS p', 'f.product_id=p.id');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');
        $this->pdo->join('order_contents as oc', 'oc.order_id = o.id', 'left');
        $this->pdo->join('order_discounts as od', 'od.order_id = o.id', 'left');
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');
        $this->pdo->join('user_fcs as uf', 'uf.user_id=u.id', 'left');
        $this->pdo->join('admins as fc', 'uf.fc_id=fc.id', 'left');

        if (!empty($this->start_date)) {
            $this->pdo->where(array('date(r.start_datetime)' => $this->start_date));
        }

        if (!empty($this->end_date)) {
            $this->pdo->where(array('date(r.end_datetime)' => $this->end_date));
        }

        if (!empty($this->display_only)) {
            $this->pdo->where('oe.id is null');
        }

        if (!empty($this->no_display_only)) {
            $this->pdo->where('oe.id is not null');
        }

        if (!empty($this->period_start_datetime) or !empty($this->period_end_datetime)) {
            if (!empty($this->period_end_datetime)) {
                $this->pdo->where('r.start_datetime <', $this->period_end_datetime);
            }

            if (!empty($this->period_start_datetime)) {
                $this->pdo->where('r.end_datetime >', $this->period_start_datetime);
            }
            $this->pdo->where('oe.id is NULL');
        } else {
            if ($this->get_end_only or $this->get_start_only) {
                if ($this->get_start_only) {
                    $this->pdo->where('r.start_datetime <= NOW() AND o.stopped=0 AND oe.id is NULL');
                }

                if ($this->get_end_only) {
                    $this->pdo->where('((r.end_datetime <= NOW() AND o.stopped=0) OR oe.id is NOT NULL)');
                }
            } else {
                if ($this->get_current_only) {
                    $this->pdo->where('(((r.start_datetime < NOW() AND r.end_datetime > NOW()) OR o.stopped=1) AND oe.id is NULL)');
                }

                if ($this->get_not_end_only) {
                    $this->pdo->where('((r.end_datetime > NOW() OR o.stopped=1) AND oe.id is NULL)');
                }
            }
        }

        if (!empty($this->facility_id)) {
            $this->pdo->where(array('r.facility_id' => $this->facility_id));
        }

        if (isset($this->product_id)) {
            if (is_array($this->product_id)) {
                $this->pdo->where_in('p.id', $this->product_id);
            } else {
                $this->pdo->where(array('p.id' => $this->product_id));
            }
        }
        
        if (!empty($this->no)) {
            $this->pdo->where(array('r.no' => $this->no));
        }        

        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->not_user_id)) {
            $this->pdo->where('o.user_id !=', $this->not_user_id);
        }

        if (!empty($this->order_id)) {
            $this->pdo->where(array('o.id' => $this->order_id));
        }

        if (!empty($this->not_order_id)) {
            $this->pdo->where('o.id !=', $this->not_order_id);
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

        if (empty($this->branch_id)) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'o.enable' => 1));
        } else {
            $this->pdo->where(array('o.branch_id' => $this->branch_id, 'o.enable' => 1));
        }

        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as r', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('orders as o', 'r.order_id = o.id');
        $this->pdo->join('users AS u', 'o.user_id=u.id');
        $this->pdo->join('facilities AS f', 'r.facility_id=f.id');
        $this->pdo->join('products AS p', 'f.product_id=p.id');
        $this->pdo->join('order_ends as oe', 'oe.order_id = o.id', 'left');

        if (isset($id)) {
            $this->pdo->where(array('r.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as r');
        }

        if (!empty($this->start_date)) {
            $this->pdo->where(array('date(r.start_datetime)' => $this->start_date));
        }

        if (!empty($this->end_date)) {
            $this->pdo->where(array('date(r.end_datetime)' => $this->end_date));
        }

        if (!empty($this->display_only)) {
            $this->pdo->where('oe.id is null');
        }

        if (!empty($this->no_display_only)) {
            $this->pdo->where('oe.id is not null');
        }

        if (!empty($this->period_start_datetime) or !empty($this->period_end_datetime)) {
            if (!empty($this->period_end_datetime)) {
                $this->pdo->where('r.start_datetime <', $this->period_end_datetime);
            }

            if (!empty($this->period_start_datetime)) {
                $this->pdo->where('r.end_datetime >', $this->period_start_datetime);
            }
            $this->pdo->where('oe.id is NULL');
        } else {
            if ($this->get_end_only or $this->get_start_only) {
                if ($this->get_start_only) {
                    $this->pdo->where('r.start_datetime <= NOW() AND o.stopped=0 AND oe.id is NULL');
                }

                if ($this->get_end_only) {
                    $this->pdo->where('((r.end_datetime <= NOW() AND o.stopped=0) OR oe.id is NOT NULL)');
                }
            } else {
                if ($this->get_current_only) {
                    $this->pdo->where('(((r.start_datetime < NOW() AND r.end_datetime > NOW()) OR o.stopped=1) AND oe.id is NULL)');
                }

                if ($this->get_not_end_only) {
                    $this->pdo->where('((r.end_datetime > NOW() OR o.stopped=1) AND oe.id is NULL)');
                }
            }
        }

        if (!empty($this->facility_id)) {
            $this->pdo->where(array('r.facility_id' => $this->facility_id));
        }

        if (isset($this->product_id)) {
            if (is_array($this->product_id)) {
                $this->pdo->where_in('p.id', $this->product_id);
            } else {
                $this->pdo->where(array('p.id' => $this->product_id));
            }
        }

        if (!empty($this->no)) {
            $this->pdo->where(array('r.no' => $this->no));
        }
        
        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->not_user_id)) {
            $this->pdo->where('o.user_id !=', $this->not_user_id);
        }

        if (!empty($this->order_id)) {
            $this->pdo->where(array('o.id' => $this->order_id));
        }

        if (!empty($this->not_order_id)) {
            $this->pdo->where('o.id !=', $this->not_order_id);
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

        if (empty($this->branch_id)) {
            $this->pdo->where(array('o.branch_id' => $this->session->userdata('branch_id'), 'o.enable' => 1));
        } else {
            $this->pdo->where(array('o.branch_id' => $this->branch_id, 'o.enable' => 1));
        }

        return $this->pdo->count_all_results($this->table . ' as r');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('r.*,p.id as product_id,p.title as product_name,oc.id as content_id,oc.content,od.total_dc_price,od.dc_rate,od.dc_price,o.user_id,o.original_price,o.price,o.payment,o.re_order,o.stopped,
        (SELECT a.transaction_date FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=r.order_id ORDER BY a.id LIMIT 1) as transaction_date,
        (SELECT SUM(if(STRCMP(a.type,"I")>0,-(a.cash),a.cash)) FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=r.order_id AND a.enable=1 GROUP BY r.id) as cash,
        (SELECT SUM(if(STRCMP(a.type,"I")>0,-(a.credit),a.credit)) FROM accounts as a INNER JOIN account_orders as ao ON a.id=ao.account_id WHERE ao.order_id=r.order_id AND a.enable=1 GROUP BY r.id) as credit,
        r.start_datetime as start_datetime,
        r.end_datetime as end_datetime,
        date(r.start_datetime) as start_date,
        date(r.end_datetime) as end_date,
      u.name AS user_name,
      u.id AS user_id,
      uac.card_no,
      u.phone,
      fc.name as fc_name,
      op.id as op_id,
      IF(DATE(r.end_datetime) < NOW(),1,0) AS expired,
      IF(DATE(r.end_datetime)<"2050-01-01",DATEDIFF(DATE(r.end_datetime),DATE(r.start_datetime)),0) as total_date,    
      IF(DATE(r.end_datetime)<"2050-01-01",DATEDIFF(DATE(r.end_datetime),CURDATE()),0) as left_date,
      IF(oe.id,1,0) as ended      
      ');

        $this->pdo->join('orders as o', 'r.order_id = o.id');
        $this->pdo->join('order_products as op', 'op.order_id = o.id');
        $this->pdo->join('order_contents AS oc', 'oc.order_id=o.id', 'left');
        $this->pdo->join('order_discounts as od', 'od.order_id = o.id', 'left');
        $this->pdo->join('users as u', 'o.user_id=u.id');
        $this->pdo->join('facilities AS f', 'r.facility_id=f.id');
        $this->pdo->join('products AS p', 'f.product_id=p.id');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');
        $this->pdo->join('user_fcs as uf', 'uf.user_id=u.id', 'left');
        $this->pdo->join('admins as fc', 'uf.fc_id=fc.id', 'left');
        $this->pdo->where(array('r.id' => $id));
        $query = $this->pdo->get($this->table . ' as r');

        return $query->row_array();
    }

    public function get_using_count($product_ids, $user_id)
    {
        $this->pdo->join('orders as o', 'r.order_id = o.id');
        $this->pdo->join('order_products as op', 'o.id = op.order_id');
        $this->pdo->where(array('o.user_id' => $user_id, 'enable' => 1));
        $this->pdo->where_in('op.product_id', $product_ids);
        $this->pdo->where('DATE(r.start_datetime)=CURDATE() AND DATE_FORMAT(r.end_datetime,"%H:%i:%s")="23:59:59"');

        return $this->pdo->count_all_results($this->table . ' as r');
    }

    public function check_use_exists($product_id, $no, $start_date = null, $end_date = null)
    {
        $this->pdo->join('orders as o', 'r.order_id = o.id');
        $this->pdo->join('facilities as f', 'r.facility_id=f.id');
        $this->pdo->join('products AS p', 'f.product_id=p.id');

        if (empty($start_date)) {
            $this->pdo->where('r.start_datetime<' . $start_date);
        } else {
            $this->pdo->where('r.start_datetime<NOW()');
        }

        if (empty($end_date)) {
            $this->pdo->where('r.end_datetime>' . $end_date);
        } else {
            $this->pdo->where('r.end_datetime>NOW()');
        }

        $this->pdo->where(array('p.id' => $product_id, 'r.no' => $no, 'o.enable' => 1));

        return $this->pdo->count_all_results($this->table . ' as r');
    }

    public function extend(array $data)
    {
        parent::extend($data);

        return $this->pdo->update($this->table, array('insert_quantity' => $data['insert_quantity'], 'end_datetime' => $data['end_date'] . ' 23:59:59'), array('id' => $data['id']));
    }

    public function update(array $data)
    {
        if (array_key_exists('start_date', $data)) {
            $data['start_datetime'] = $data['start_date'] . ' 00:00:01';
            unset($data['start_date']);
        }

        if (array_key_exists('end_date', $data)) {
            $data['end_datetime'] = $data['end_date'] . ' 23:59:59';
            unset($data['end_date']);
        }

        return parent::update($data);
    }

    public function end($id, $end_date = null, $start_date_too =false)
    {
        if (empty($end_date)) {
            $end_date = $this->today;
        }

        if(empty($start_date_too)) {
            $result=$this->pdo->update($this->table, array('end_datetime' => $end_date), array('id' => $id));
        } else {
            $result=$this->pdo->update($this->table, array('start_datetime'=>$end_date,'end_datetime' => $end_date), array('id' => $id));
        }

        return $result;
    }
}
