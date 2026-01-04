<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class OrderStop extends SL_SubModel
{
    protected $table = 'order_stops';
    protected $parent_id_name = 'order_id';
    protected $accepted_attributes = array('user_stop_id', 'order_id', 'stop_start_date', 'stop_end_date', 'is_change_start_date', 'stop_day_count', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select = 'os.*,p.title as product_name,pc.title as product_category_name,os.stop_day_count,os.stop_start_date,os.stop_end_date,oc.id as content_id,oc.content,
                    if(e.id,e.start_date,if(r.id,date(r.start_datetime), if(rs.id,rs.start_date,null) ) ) as start_date,
                      if(e.id,e.end_date,if(r.id,date(r.end_datetime), if(rs.id,rs.end_date,null) ) ) as end_date,
                      DATEDIFF(if(e.id,e.end_date,if(r.id,date(r.end_datetime), if(rs.id,rs.end_date,null) ) ),os.stop_start_date) as day_count,
                      if(os.stop_day_count,DATE_ADD(if(e.id,e.end_date,if(r.id,date(r.end_datetime), if(rs.id,rs.end_date,null) ) ),INTERVAL os.stop_day_count Day),null) as change_end_date,
                      us.request_date
                      ';

        $this->pdo->select($select);
        $this->pdo->join('user_stops as us', 'os.user_stop_id=us.id');
        $this->pdo->join('orders as o', 'os.order_id = o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id = p.id');
        $this->pdo->join('order_contents as oc', 'oc.order_id=o.id', 'left');
        $this->pdo->join('product_categories as pc', 'p.product_category_id = pc.id', 'left');
        if (empty($this->enroll)) {
            $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
        } else {
            $this->pdo->join('enrolls as e', 'e.order_id=o.id');
        }
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
        $this->pdo->join('rent_sws as rs', 'rs.order_id=o.id', 'left');


        if (!empty($this->rent)) {
            $this->pdo->join('rents as r', 'r.order_id=o.id');
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->order_id)) {
            $this->pdo->where(array('os.order_id' => $this->order_id));
        }

        if (!empty($this->current_only)) {
            $this->pdo->where('os.stop_start_date<=CURDATE()');
        }

        if (empty($this->disable)) {
            $this->pdo->where(array('os.enable' => 1, 'o.stopped' => 1));
        } else {
            $this->pdo->where(array('oss.execute' => 0, 'os.enable' => 0));
        }

        $this->pdo->where(array('o.enable' => 1, 'o.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->group_by('os.id');
        $this->pdo->order_by($order, $desc);
        $query = $this->pdo->get($this->table . ' as os');

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*) as count');
        $this->pdo->join('user_stops as us', 'os.user_stop_id=us.id');
        $this->pdo->join('orders as o', 'os.order_id = o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id = p.id');
        $this->pdo->join('product_categories as cc', 'p.product_category_id = cc.id', 'left');

        if (isset($id)) {
            $this->pdo->where(array('os.id' => $id));

            return $this->pdo->count_all_results($this->table . ' as os');
        }

        if (!empty($this->enroll)) {
            $this->pdo->join('enrolls as e', 'e.order_id=o.id');
        }

        if (!empty($this->rent)) {
            $this->pdo->join('rents as r', 'r.order_id=o.id');
        }

        if (!empty($this->user_id)) {
            $this->pdo->where(array('o.user_id' => $this->user_id));
        }

        if (!empty($this->order_id)) {
            $this->pdo->where(array('os.order_id' => $this->order_id));
        }

        if (!empty($this->current_only)) {
            $this->pdo->where('os.stop_start_date<=CURDATE()');
        }

        if (empty($this->disable)) {
            $this->pdo->where(array('os.enable' => 1, 'o.stopped' => 1));
        } else {
            $this->pdo->where(array('oss.execute' => 0, 'os.enable' => 0));
        }

        $this->pdo->where(array('o.enable' => 1, 'o.branch_id' => $this->session->userdata('branch_id')));
        $this->pdo->group_by('os.id');

        return $this->pdo->count_all_results($this->table . ' as os');
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('os.*,o.user_id,p.title as course_name,cc.title as course_category_name,os.stop_day_count,os.stop_start_date,os.stop_end_date,oc.id as content_id,oc.content,
        if(e.id,e.start_date,if(r.id,date(r.start_datetime), if(rs.id,rs.start_date,null) ) ) as start_date,
        if(e.id,e.end_date,if(r.id,date(r.end_datetime), if(rs.id,rs.end_date,null) ) ) as end_date,
        DATEDIFF(if(e.id,e.end_date,if(r.id,date(r.end_datetime), if(rs.id,rs.end_date,null) ) ),os.stop_start_date) as day_count,
        if(os.stop_day_count,DATE_ADD(if(e.id,e.end_date,if(r.id,date(r.end_datetime), if(rs.id,rs.end_date,null) ) ),INTERVAL os.stop_day_count Day),null) as change_end_date,
        us.request_date,
        uss.content
        ');

        $this->pdo->join('user_stops as us', 'os.user_stop_id=us.id');
        $this->pdo->join('user_stop_contents as uss', 'uss.user_stop_id=us.id', 'left');
        $this->pdo->join('orders as o', 'os.order_id=o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id = p.id');
        $this->pdo->join('order_contents as oc', 'oc.order_id=o.id', 'left');
        $this->pdo->join('product_categories as cc', 'p.product_category_id = cc.id', 'left');
        $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
        $this->pdo->join('rent_sws as rs', 'rs.order_id=o.id', 'left');
        $this->pdo->where(array('os.id' => $id));
        $query = $this->pdo->get($this->table . ' as os');

        return $query->row_array();
    }

    public function get_count_by_parent_id($parent_id)
    {
        $this->pdo->join('orders as o', 'os.order_id=o.id');
        $this->pdo->where(array('os.' . $this->parent_id_name => $parent_id));

        return $this->pdo->count_all_results($this->table . ' as os');
    }

    public function get_content_by_parent_id($parent_id)
    {
        if (!$this->get_count_by_parent_id($parent_id)) {
            return false;
        }

        $this->pdo->select('os.*,o.user_id,p.title as course_name,cc.title as course_category_name,os.stop_day_count,os.stop_start_date,os.stop_end_date,oc.id as content_id,oc.content,
        if(e.id,e.start_date,if(r.id,date(r.start_datetime), if(rs.id,rs.start_date,null) ) ) as start_date,
        if(e.id,e.end_date,if(r.id,date(r.end_datetime), if(rs.id,rs.end_date,null) ) ) as end_date,
        DATEDIFF(if(e.id,e.end_date,if(r.id,date(r.end_datetime), if(rs.id,rs.end_date,null) ) ),os.stop_start_date) as day_count,
        if(os.stop_day_count,DATE_ADD(if(e.id,e.end_date,if(r.id,date(r.end_datetime), if(rs.id,rs.end_date,null) ) ),INTERVAL os.stop_day_count Day),null) as change_end_date,
        us.request_date,
        uss.content
        ');

        $this->pdo->join('user_stops as us', 'os.user_stop_id=us.id');
        $this->pdo->join('user_stop_contents as uss', 'uss.user_stop_id=us.id', 'left');
        $this->pdo->join('orders as o', 'os.order_id=o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id = p.id');
        $this->pdo->join('order_contents as oc', 'oc.order_id=o.id', 'left');
        $this->pdo->join('product_categories as cc', 'p.product_category_id = cc.id', 'left');
        $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
        $this->pdo->join('rent_sws as rs', 'rs.order_id=o.id', 'left');
        $this->pdo->where(array('os.' . $this->parent_id_name => $parent_id));
        $this->pdo->order_by('os.id', 'desc');
        $query = $this->pdo->get($this->table . ' as os');

        return $query->row_array();
    }
}
