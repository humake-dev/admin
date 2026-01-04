<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class NoFc extends SL_Model
{
    protected $table = 'users';
    protected $quantity_only = false;
    protected $search_param;
    protected $status='all';
    protected $accepted_attributes = array('branch_id', 'admin_id', 'title', 'enable', 'created_at', 'updated_at');

    public function insert_fc($fc_id,$user_id)
    {
        return $this->pdo->insert('user_fcs', ['fc_id' => $fc_id,'user_id' => $user_id,'created_at'=>$this->now, 'updated_at' => $this->now]);
    }

    protected function set_search($select = false)
    {
        $this->pdo->join('orders as o','o.user_id=u.id','left');
        $this->pdo->join('order_products as op','op.order_id=o.id','left');
        $this->pdo->join('enrolls as e','e.order_id=o.id','left');
        $this->pdo->join('courses as c','op.product_id=c.product_id','left');
        $this->pdo->join('user_fcs as ufc','ufc.user_id=u.id','left');
        $this->pdo->join('order_ends as oe','oe.order_id=o.id','left');
        $this->pdo->join('product_relations as pr','pr.product_id=op.product_id','left');
        $this->pdo->where('ufc.id is null');
        
        if(!empty($this->period)) {
            if($this->period!='-') {
                $this->pdo->where(array('pr.product_relation_type_id'=>4,'o.enable'=>1));
                
                if($this->period!=1) {
                    $this->pdo->where(array('c.lesson_period_unit'=>'M'));
                }
            }
            
            switch($this->period) {
                case '-':
                    $this->pdo->having('SUM(case when (e.id is not null AND pr.product_relation_type_id=4 AND o.enable=1) then 1 else 0 end) = 0',null,false);                 
                    break;
                case 1:
                    $this->pdo->having('count(o.id)>0',null,false);
                    $this->pdo->having('SUM(case when (e.insert_quantity>=3 AND c.lesson_period_unit="M") then 1 else 0 end) = 0',null,false);
                    break;
                case 3:
                    $this->pdo->where('e.insert_quantity>=',3,false);
                    $this->pdo->where('e.insert_quantity<',6,false);
                    $this->pdo->having('SUM(case when (e.insert_quantity>=6) then 1 else 0 end) = 0',null,false);
                    break;
                case 6:
                    $this->pdo->where('e.insert_quantity>=',6,false);
                    $this->pdo->where('e.insert_quantity<',12,false);
                    $this->pdo->having('SUM(case when (e.insert_quantity>=12) then 1 else 0 end) = 0',null,false);                        
                    break;
                case 12:
                    $this->pdo->where('e.insert_quantity>=',12,false);
                    $this->pdo->where('e.insert_quantity<',24,false);
                    $this->pdo->having('SUM(case when (e.insert_quantity>=24) then 1 else 0 end) = 0',null,false);
                    break;
                case 24:
                    $this->pdo->where('e.insert_quantity>=',24,false);
                    break;
            }
        }
        
        switch($this->status) {
            case 'expired':
                $this->pdo->having('SUM(case when (o.enable=1 AND oe.id is null AND pr.product_relation_type_id=4 AND
                if(o.stopped=1,(SELECT if(os.stop_day_count,DATE_ADD(e.end_date,INTERVAL os.stop_day_count Day),null) FROM order_stops AS os WHERE os.order_id=o.id AND os.enable=1 ORDER BY os.id desc LIMIT 1),e.end_date)
                >=CURDATE()) then 1 else 0 end) <= 0');
                break;
            case 'available':
                $this->pdo->having('SUM(case when (o.enable=1 AND oe.id is null AND pr.product_relation_type_id=4 AND 
                if(o.stopped=1,(SELECT if(os.stop_day_count,DATE_ADD(e.end_date,INTERVAL os.stop_day_count Day),null) FROM order_stops AS os WHERE os.order_id=o.id AND os.enable=1 ORDER BY os.id desc LIMIT 1),e.end_date)
                >=CURDATE()) then 1 else 0 end) > 0');
                break;
        }
        
        $this->pdo->where(['u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1]);
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if(empty($this->id_only)) {
            $this->pdo->select('u.id,u.*,
            (SELECT concat(enrolls.insert_quantity,"::",courses.lesson_period_unit,"::",SUM(if(accounts.type="I",(accounts.cash+accounts.credit),-(accounts.cash+accounts.credit))),"::",min(orders.transaction_date)) FROM orders INNER JOIN enrolls ON enrolls.order_id=orders.id INNER JOIN courses ON enrolls.course_id=courses.id LEFT JOIN account_orders ON account_orders.order_id=orders.id LEFT JOIN accounts ON account_orders.account_id=accounts.id LEFT JOIN product_relations ON product_relations.product_id=courses.product_id WHERE orders.id=o.id AND product_relations.product_relation_type_id=4 AND orders.enable=1 GROUP BY orders.user_id ORDER BY IF(enrolls.start_date<=CURDATE() AND enrolls.end_date>=CURDATE(),3,IF(enrolls.start_date>=CURDATE() AND enrolls.end_date>=CURDATE(),2,0)) DESC, enrolls.have_datetime DESC LIMIT 1) as oo,
            SUM(case when o.enable=1 AND oe.id IS NULL AND pr.product_relation_type_id=4 AND e.end_date>=CURDATE() then 1 else 0 end) as available_count,
            ufc.fc_id', false);
        } else {
            $this->pdo->select('u.id');
        }


        
        $this->set_search(true);
        
        $this->pdo->group_by('u.id');
        $this->pdo->order_by('u.' . $order, $desc);
        $query = $this->pdo->get($this->table . ' as u', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(['u.id' => $id]);

            return $this->pdo->count_all_results($this->table . ' as u');
        }

        $this->pdo->select('count(*) as count');
        $this->set_search();
        $this->pdo->group_by('u.id');

        return $this->pdo->count_all_results($this->table . ' as u');
    }
}
