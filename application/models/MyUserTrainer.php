<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class MyUserTrainer extends SL_Model
{
    protected $table = 'users';
    protected $pt_type= 'remain';

    protected function set_search($select = false)
    {
        if (!empty($this->search_start_date)) {
            $this->pdo->where('o.transaction_date >="'.$this->search_start_date.'"');
        }

        if (!empty($this->search_end_date)) {
            $this->pdo->where('o.transaction_date <="'.$this->search_end_date.'"');
        }

        if($this->pt_type=='expired') {
            $this->pdo->where('(e.quantity-e.use_quantity)<=0');
           // $this->pdo->having('SUM(e.quantity-e.use_quantity)<=0');
        } else {
            $this->pdo->where('(e.quantity-e.use_quantity)>0');
           // $this->pdo->having('SUM(e.quantity-e.use_quantity)>0');
        }
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        if($this->pt_type=='expired') {  
            $select = 'u.id,p.title as product_name,SUM(e.quantity) as quantity,SUM(e.use_quantity) as use_quantity,
            (SELECT concat(SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit)))) FROM accounts as a INNER JOIN account_orders ON a.id=account_orders.account_id INNER JOIN orders ON account_orders.order_id=orders.id INNER JOIN enrolls ON enrolls.order_id=orders.id INNER JOIN courses ON enrolls.course_id=courses.id INNER JOIN products ON courses.product_id=products.id WHERE u.id=a.user_id AND courses.lesson_type=4 AND orders.enable=1 AND a.enable=1 AND a.account_category_id!='.ADD_COMMISSION.' AND (enrolls.quantity-enrolls.use_quantity)<=0 GROUP BY orders.user_id ORDER BY enrolls.have_datetime DESC LIMIT 1) as account,
            u.name as user_name,max(o.transaction_date) as transaction_date,
            u.phone as phone';
        } else {
            $select = 'u.id,p.title as product_name,SUM(e.quantity) as quantity,SUM(e.use_quantity) as use_quantity,
            (SELECT concat(SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit)))) FROM accounts as a INNER JOIN account_orders ON a.id=account_orders.account_id INNER JOIN orders ON account_orders.order_id=orders.id INNER JOIN enrolls ON enrolls.order_id=orders.id INNER JOIN courses ON enrolls.course_id=courses.id INNER JOIN products ON courses.product_id=products.id WHERE u.id=a.user_id AND courses.lesson_type=4 AND orders.enable=1 AND a.enable=1 AND a.account_category_id!='.ADD_COMMISSION.' AND (enrolls.quantity-enrolls.use_quantity)>0 GROUP BY orders.user_id ORDER BY enrolls.have_datetime DESC LIMIT 1) as account,
            u.name as user_name,max(o.transaction_date) as transaction_date,
            u.phone as phone';
        }

        $this->pdo->select($select);

        $this->pdo->join('orders as o', 'o.user_id = u.id');
        $this->pdo->join('enrolls as e', 'e.order_id = o.id');
        $this->pdo->join('enroll_trainers as et', 'et.enroll_id = e.id');
        $this->pdo->join('admins', 'et.trainer_id = admins.id');
        $this->pdo->join('courses as c', 'e.course_id = c.id');
        $this->pdo->join('products as p', 'c.product_id = p.id');
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');

        $this->set_search();

        $this->pdo->where('oe.id is NULL');
        $this->pdo->where(array('et.trainer_id'=>$this->session->userdata('admin_id'),'admins.enable'=>1,'u.enable'=>1,'o.enable'=>1,'c.lesson_type'=>4));
        $this->pdo->group_by('u.id');
        $this->pdo->order_by('max(o.transaction_date) desc');
        $query = $this->pdo->get($this->table . ' as u', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(['u.id' => $id]);

            return $this->pdo->count_all_results($this->table . ' as u');
        }

        $this->pdo->select('count(distinct o.user_id)');

        $this->pdo->join('orders as o', 'o.user_id = u.id');
        $this->pdo->join('enrolls as e', 'e.order_id = o.id');
        $this->pdo->join('enroll_trainers as et', 'et.enroll_id = e.id');
        $this->pdo->join('admins', 'et.trainer_id = admins.id');
        $this->pdo->join('courses as c', 'e.course_id = c.id');       
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');

        $this->set_search();
        
        $this->pdo->where('oe.id is NULL');        
        $this->pdo->where(array('et.trainer_id'=>$this->session->userdata('admin_id'),'admins.enable'=>1,'u.enable'=>1,'o.enable'=>1,'c.lesson_type'=>4));
        $this->pdo->group_by('u.id');

        return $this->pdo->count_all_results($this->table . ' as u');
    }
}
