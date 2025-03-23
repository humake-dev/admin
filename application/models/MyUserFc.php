<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class MyUserFc extends SL_Model
{
    protected $table = 'users';

    protected function set_search($select = false)
    {
        if (!empty($this->search_start_date)) {
            $this->pdo->where('a.transaction_date >="'.$this->search_start_date.'"');
        }

        if (!empty($this->search_end_date)) {
            $this->pdo->where('a.transaction_date <="'.$this->search_end_date.'"');
        }

        if (empty($this->refund)) {
            $this->pdo->where_not_in('a.account_category_id',array(7,8,24));
        } else {
            $this->pdo->where(array('a.type'=>'O'));
            $this->pdo->where_in('a.account_category_id',array(7,8,24));
        }
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $select='u.*,ufc.fc_id,fc.name as fc_name,max(a.transaction_date) as transaction_date
        ,GROUP_CONCAT(DISTINCT(CONCAT(IF(p.id=10,oo.title,p.title),"::",if(p.id=10,op.quantity,if(e.id,e.insert_quantity,if(r.id,r.insert_quantity,""))),"::",if(p.id=10,"",if(c.id,c.lesson_period_unit,if(r.id,"M",""))),"::",o.id))) as products';
        
        $this->pdo->select($select.',SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit))) as account', false);    
        $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id');
        $this->pdo->join('admins AS fc', 'ufc.fc_id=fc.id');

        $this->pdo->join('orders AS o', 'o.user_id=u.id','left');           
        $this->pdo->join('order_products AS op', 'op.order_id=o.id','left');         
        $this->pdo->join('products AS p', 'op.product_id=p.id','left');       
        $this->pdo->join('courses AS c', 'c.product_id=p.id', 'left');
        $this->pdo->join('enrolls AS e', 'e.order_id=o.id', 'left');
        $this->pdo->join('rents AS r', 'r.order_id=o.id', 'left');
        $this->pdo->join('rent_sws AS rs', 'rs.order_id=o.id', 'left');        
        $this->pdo->join('others AS oo', 'oo.order_id=o.id', 'left');               
     
        $this->pdo->join('account_orders AS ao', 'ao.order_id=o.id','left'); 
        $this->pdo->join('accounts AS a', 'ao.account_id=a.id','left');           

        $this->set_search();

        $this->pdo->where(array('ufc.fc_id' => $this->session->userdata('admin_id'), 'a.branch_id' => $this->session->userdata('branch_id'),'a.enable' => 1,'u.enable'=>1));
        $this->pdo->where('a.account_category_id !=25 AND (c.lesson_type!=4 OR c.lesson_type is null)');

        $this->pdo->group_by('a.user_id');
        $this->pdo->order_by('max(a.transaction_date)', $desc);
        $query = $this->pdo->get($this->table . ' as u', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        if (isset($id)) {
            $this->pdo->where(['u.id' => $id]);

            return $this->pdo->count_all_results($this->table . ' as u');
        }

        $this->pdo->select('count(distinct u.id)');
        $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id');
        $this->pdo->join('admins AS fc', 'ufc.fc_id=fc.id');
        $this->pdo->join('accounts AS a', 'a.user_id=u.id');        
        $this->pdo->join('account_orders AS ao', 'ao.account_id=a.id','left'); 
        $this->pdo->join('orders AS o', 'ao.order_id=o.id','left');           
        $this->pdo->join('order_products AS op', 'op.order_id=o.id','left');         
        $this->pdo->join('products AS p', 'op.product_id=p.id','left');       
        $this->pdo->join('courses AS c', 'c.product_id=p.id', 'left');

        $this->set_search();

        $this->pdo->where(array('ufc.fc_id' => $this->session->userdata('admin_id'), 'a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1,'u.enable'=>1));
        $this->pdo->where('a.account_category_id !=25 AND (c.lesson_type!=4 OR c.lesson_type is null)');
        $this->pdo->group_by('a.user_id');

        return $this->pdo->count_all_results($this->table . ' as u');
    }
}
