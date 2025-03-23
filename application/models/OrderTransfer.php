<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class OrderTransfer extends SL_SubModel
{
    protected $table = 'order_transfers';
    protected $parent_id_name = 'order_id';
    protected $table_content = 'order_transfer_contents';
    protected $table_content_required = false;
    protected $table_id_name = 'order_transfer_id';
    protected $accepted_attributes = array('order_id', 'giver_id', 'recipient_id', 'same', 'give_count', 'origin_start_date', 'origin_end_date', 'origin_quantity','transfer_date', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select = 'ot.*,otc.id as content_id,otc.content,pc.title as product_category,if(ots.schedule_date,ots.schedule_date,ot.transfer_date) as transfer_date,giver.name as giver_name,recipient.name as recipient_name,p.title as product_name,otob.origin_branch_id,otob.transfer_branch_id,b.title as origin_branch_name,tb.title as transfer_branch_name';

        if (!empty($this->enroll)) {
            $select .= ',e.start_date,e.end_date,e.quantity,c.lesson_type';
        }

        if (!empty($this->rent)) {
            $select .= ',(r.start_datetime) as start_date,(r.start_datetime) as end_date';
        }

        $this->pdo->select($select);
        $this->pdo->join('orders as o', 'ot.order_id = o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id = p.id');
        $this->pdo->join('users as giver', 'ot.giver_id=giver.id');
        $this->pdo->join('users as recipient', 'ot.recipient_id=recipient.id');
        $this->pdo->join('product_categories as pc', 'p.product_category_id = pc.id', 'left');
        $this->pdo->join('order_transfer_schedules as ots', 'ots.order_transfer_id=ot.id', 'left');
        $this->pdo->join('order_transfer_contents as otc', 'otc.order_transfer_id=ot.id', 'left');
        $this->pdo->join('order_transfer_other_branches as otob', 'otob.order_transfer_id=ot.id', 'left');
        $this->pdo->join('branches as b', 'otob.origin_branch_id=b.id', 'left');
        $this->pdo->join('branches as tb', 'otob.transfer_branch_id=tb.id', 'left');
        
        if (!empty($this->enroll)) {
            $this->pdo->join('enrolls as e', 'e.order_id=o.id');
            $this->pdo->join('courses as c', 'c.product_id = p.id');
        }

        if (!empty($this->rent)) {
            $this->pdo->join('rents as r', 'r.order_id=o.id');
        }

        if (!empty($this->user_id)) {
            $this->pdo->where('(ot.giver_id=' . $this->user_id . ' OR ot.recipient_id=' . $this->user_id . ')');
        }

        //$this->pdo->where(array('ot.enable'=>1));
        $this->pdo->order_by('id', 'desc');
        $query = $this->pdo->get($this->table . ' as ot');
        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->join('orders as o', 'ot.order_id = o.id');
        $this->pdo->join('order_products as op', 'op.order_id=o.id');
        $this->pdo->join('products as p', 'op.product_id = p.id');

        if (isset($id)) {
            $this->pdo->where(array('ot.id' => $id));
        }

        if (!empty($this->enroll)) {
            $this->pdo->join('enrolls as e', 'e.order_id=o.id');
        }

        if (!empty($this->rent)) {
            $this->pdo->join('rents as r', 'r.order_id=o.id');
        }

        if (!empty($this->user_id)) {
            $this->pdo->where('(ot.giver_id=' . $this->user_id . ' OR ot.recipient_id=' . $this->user_id . ')');
        }

       //$this->pdo->where(array('ot.enable'=>1));
        return $this->pdo->count_all_results($this->table . ' as ot');
    }
}
