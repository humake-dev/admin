<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Account extends SL_Model
{
    protected $table = 'accounts';
    protected $accepted_attributes = array('account_category_id', 'branch_id', 'user_id', 'type', 'transaction_date', 'cash', 'credit', 'enable', 'created_at', 'updated_at');

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->pdo->select('a.transaction_date,ufc.fc_id,fc.name as fc_name,oo.title as other_title,p.title as product_title,
            (a.cash+a.credit) as total_account,
            o.price,
            o.original_price,
            o.transaction_date as order_transaction_date,
            p.id as product_id,
            IF(p.id,p.title,null) as product_name,
            IF(pc.id,pc.title,null) as product_category,
            IF(r.id,"Rent",IF(e.id,"Enroll","Product")) as order_type,
            IF(e.start_date,e.start_date,if(r.id,if(r.start_datetime,date(r.start_datetime),""),"")) as start_date,
            IF(e.end_date,e.end_date,if(r.id,if(r.end_datetime,date(r.end_datetime),""),"")) as end_date,
            IF(e.id,(select SUM(cash) FROM accounts INNER JOIN account_commissions ON account_commissions.account_id=accounts.id WHERE account_commissions.enroll_id=e.id),0) as commission,
            u.name,uac.card_no,a.*,e.quantity,e.use_quantity,ac.title as category_name,
            r.no,r.start_datetime,r.end_datetime
            ', false);

        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id');
        $this->pdo->join('users as u', 'a.user_id=u.id', 'left');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id = u.id', 'left');
        $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id', 'left');
        $this->pdo->join('admins as fc', 'ufc.fc_id=fc.id', 'left');

        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id', 'left');
        $this->pdo->join('orders as o', 'ao.order_id=o.id', 'left');
        $this->pdo->join('account_products as ap', 'ap.account_id=a.id', 'left');
        $this->pdo->join('products as p', 'ap.product_id=p.id', 'left');
        $this->pdo->join('product_categories as pc', 'p.product_category_id=pc.id', 'left');

        $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
        $this->pdo->join('others as oo', 'oo.order_id=o.id', 'left');

        if (isset($this->date)) {
            $this->pdo->where(array('a.transaction_date' => $this->date));
        }

        if (isset($this->user_id)) {
            $this->pdo->where(array('a.user_id' => $this->user_id));
        }

        if (isset($this->order_id)) {
            $this->pdo->select('od.dc_rate,od.dc_price');
            $this->pdo->join('order_discounts as od', 'od.order_id=o.id', 'left');
            $this->pdo->where(array('o.id' => $this->order_id));
        }

        if (!isset($this->user_id) and !isset($this->order_id)) {
            $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));
        }

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        if (isset($this->no_commission)) {
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION));
        }

        if (isset($this->no_branch_transfer)) {
            $this->pdo->where_not_in('a.account_category_id', array(BRANCH_TRANSFER));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        $this->pdo->order_by('a.id', 'desc');
        //$this->pdo->group_by('a.id');

        $query = $this->pdo->get($this->table . ' as a', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*) as count');
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id');

        if (isset($this->date)) {
            $this->pdo->where(array('a.transaction_date' => $this->date));
        }

        if (isset($this->user_id)) {
            $this->pdo->where(array('a.user_id' => $this->user_id));
        }

        if (isset($this->order_id)) {
            $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
            $this->pdo->where(array('ao.order_id' => $this->order_id));
        }

        if (!isset($this->user_id) and !isset($this->order_id)) {
            $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id')));
        }

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        if (isset($id)) {
            $this->pdo->where(array('a.id' => $id));
        }

        if (isset($this->no_commission)) {
            $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION));
        }

        if (isset($this->no_branch_transfer)) {
            $this->pdo->where_not_in('a.account_category_id', array(BRANCH_TRANSFER));
        }

        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        $this->pdo->group_by('a.id');

        return $this->pdo->count_all_results($this->table . ' as a');
    }

    public function insert(array $data)
    {
        if (empty($data['user_id'])) {
            throw new Exception('user id not insert');
        }

        $cash = 0;
        $credit = 0;

        if (!empty($data['cash'])) {
            $cash = $data['cash'];
        }

        if (!empty($data['credit'])) {
            $credit = $data['credit'];
        }

        if (empty($data['transaction_date'])) {
            $data['transaction_date'] = $this->today;
        }

        if (isset($data['type'])) {
            if ($data['type'] != 'O') {
                $data['type'] = 'I';
            }
        } else {
            $data['type'] = 'I';
        }

        $data['payment'] = $cash + $credit;
        $id = parent::insert($data);

        $this->pdo->insert('account_orders', array('account_id' => $id, 'order_id' => $data['order_id']));

        if (empty($data['product_id'])) {
            log_message('error', 'Some variable did not contain a value.' . basename($_SERVER['SCRIPT_FILENAME']));
        } else {
            $this->pdo->insert('account_products', array('account_id' => $id, 'product_id' => $data['product_id']));
        }

        if (!empty($data['order_transfer_id'])) {
            $this->pdo->insert('account_order_transfers', array('account_id' => $id, 'order_transfer_id' => $data['order_transfer_id']));
        }

        if ($data['account_category_id'] == ADD_COMMISSION) {
            $this->pdo->insert('account_commissions', array('account_id' => $id, 'course_id' => $data['course_id'], 'enroll_id' => $data['enroll_id'], 'employee_id' => $data['employee_id']));
        }

        return $id;
    }

    public function get_refund($per_page, $page)
    {
        $result = array();
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.type' => 'O', 'a.enable' => 1));
        $this->pdo->where_in('a.account_category_id',array(7,8,24));
        
        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        $result['total'] = $this->pdo->count_all_results($this->table . ' as a');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('a.*,u.name as user_name,ac.title as account_category_name');
        $this->pdo->join('users as u', 'a.user_id=u.id', 'left');
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id', 'left');
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.type' => 'O', 'a.enable' => 1));
        $this->pdo->where_in('a.account_category_id',array(7,8,24));        

        if (isset($this->start_date)) {
            $this->pdo->where('a.transaction_date >=', $this->start_date);
        }

        if (isset($this->end_date)) {
            $this->pdo->where('a.transaction_date <=', $this->end_date);
        }

        $this->pdo->order_by('a.id', 'desc');
        $query = $this->pdo->get($this->table . ' as a', $per_page, $page);
        $result['list'] = $query->result_array();

        return $result;
    }

    public function get_fc_total_sales($start_date, $end_date)
    {
        $this->pdo->join('users as u', 'a.user_id=u.id');
        $this->pdo->join('user_fcs as ufc', 'ufc.user_id=u.id');
        $this->pdo->join('account_products AS ap', 'ap.account_id=a.id');
        $this->pdo->join('courses AS c', 'c.product_id=ap.product_id', 'left');
        $this->pdo->where(array('ufc.fc_id' => $this->session->userdata('admin_id'), 'a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        $this->pdo->where('a.account_category_id !=25 AND (c.lesson_type!=4 OR c.lesson_type is null)');
        $this->pdo->where('a.transaction_date>="' . $start_date . '" AND a.transaction_date<="' . $end_date . '"');
        $count = $this->pdo->count_all_results($this->table . ' as a');

        if (empty($count)) {
            return 0;
        }

        $this->pdo->select('SUM(if(a.type="I",(a.cash+a.credit),IF(a.account_category_id=4 OR a.account_category_id=5 OR a.account_category_id=6,-(a.cash+a.credit),0))) as total');
        $this->pdo->join('users as u', 'a.user_id=u.id');
        $this->pdo->join('user_fcs as ufc', 'ufc.user_id=u.id');   
        $this->pdo->join('account_products AS ap', 'ap.account_id=a.id');
        $this->pdo->join('courses AS c', 'c.product_id=ap.product_id', 'left');

        $this->pdo->where(array('ufc.fc_id' => $this->session->userdata('admin_id'), 'a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        $this->pdo->where('a.transaction_date>="' . $start_date . '" AND a.transaction_date<="' . $end_date . '"');
        $this->pdo->where('a.account_category_id !=25 AND (c.lesson_type!=4 OR c.lesson_type is null)');
        $query = $this->pdo->get($this->table . ' as a');
        $result = $query->row_array();

        return $result['total'];
    }

    public function get_fc_total_refund($start_date, $end_date)
    {
        $this->pdo->join('users as u', 'a.user_id=u.id');
        $this->pdo->join('user_fcs as ufc', 'ufc.user_id=u.id');   
        $this->pdo->join('account_products AS ap', 'ap.account_id=a.id');
        $this->pdo->join('courses AS c', 'c.product_id=ap.product_id', 'left');
        $this->pdo->where(array('ufc.fc_id' => $this->session->userdata('admin_id'), 'a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        $this->pdo->where('a.transaction_date>="' . $start_date . '" AND a.transaction_date<="' . $end_date . '"');
        $this->pdo->where('a.account_category_id not in(4,5,6,25) AND (c.lesson_type is null OR c.lesson_type!=4)');
        $count = $this->pdo->count_all_results($this->table . ' as a');

        if (empty($count)) {
            return 0;
        }

        $this->pdo->select('sum(if(a.type="O",(a.cash+a.credit),0)) as total');
        $this->pdo->join('users as u', 'a.user_id=u.id');
        $this->pdo->join('user_fcs as ufc', 'ufc.user_id=u.id');   
        $this->pdo->join('account_products AS ap', 'ap.account_id=a.id');
        $this->pdo->join('courses AS c', 'c.product_id=ap.product_id', 'left');
        $this->pdo->where(array('ufc.fc_id' => $this->session->userdata('admin_id'), 'a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        $this->pdo->where('a.transaction_date>="' . $start_date . '" AND a.transaction_date<="' . $end_date . '"');
        $this->pdo->where('a.account_category_id not in(4,5,6,25) AND (c.lesson_type is null OR c.lesson_type!=4)');
        $query = $this->pdo->get($this->table . ' as a');
        $result = $query->row_array();

        return $result['total'];
    }    

    public function get_product_content($id, $per_page, $page)
    {
        $result = array();
        $this->pdo->select('count(*)');
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id');

        $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'p.id' => $id, 'a.enable' => 1));

        $result['total'] = $this->pdo->count_all_results($this->table . ' as a');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('a.*,u.name as user_name,ac.title as account_category_name');
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id');
        $this->pdo->join('users as u', 'a.user_id=u.id', 'left');
        $this->pdo->join('admins as fc', 'u.fc_id=fc.id', 'left');

        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id', 'left');
        $this->pdo->join('orders as o', 'ao.order_id=o.id', 'left');
        $this->pdo->join('order_products as op', 'op.order_id=o.id', 'left');
        $this->pdo->join('products as p', 'op.product_id=p.id', 'left');

        $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'p.id' => $id, 'a.enable' => 1));
        $this->pdo->order_by('a.id desc');
        $query = $this->pdo->get($this->table . ' as a', $per_page, $page);
        $result['list'] = $query->result_array();

        return $result;
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('a.*,u.name as user_name');
        $this->pdo->join('users as u', 'a.user_id=u.id', 'left');
        $this->pdo->where(array('a.id' => $id));
        $query = $this->pdo->get($this->table . ' as a');

        return $query->row_array();
    }

    public function get_product_content_other($type, $per_page, $page)
    {
        $result = array();
        $this->pdo->select('count(*)');
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id');
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
        $this->pdo->join('orders as o', 'ao.order_id=o.id');
        $this->pdo->join('others as oo', 'oo.other_id=o.id');
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));

        $result['total'] = $this->pdo->count_all_results($this->table . ' as a');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('a.*,u.name as user_name,ac.title as account_category_name,oo.title as product_name');
        $this->pdo->join('account_categories as ac', 'a.account_category_id=ac.id');
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
        $this->pdo->join('orders as o', 'ao.order_id=o.id');
        $this->pdo->join('others as oo', 'oo.other_id=o.id');
        $this->pdo->join('users as u', 'a.user_id=u.id', 'left');
        $this->pdo->where(array('a.branch_id' => $this->session->userdata('branch_id'), 'a.enable' => 1));
        $this->pdo->order_by('a.id desc');
        $query = $this->pdo->get($this->table . ' as a', $per_page, $page);
        $result['list'] = $query->result_array();

        return $result;
    }

    public function get_content_by_category_id($category_id, $p_id)
    {
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
        $this->pdo->join('orders as o', 'ao.order_id=o.id');
        $this->pdo->where(array('account_category_id' => $category_id, 'o.id' => $p_id));
        $count = $this->pdo->count_all_results($this->table . ' as a');

        if (!$count) {
            return false;
        }

        $this->pdo->select('a.*,ao.order_id');
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
        $this->pdo->join('orders as o', 'ao.order_id=o.id');
        $this->pdo->where(array('account_category_id' => $category_id, 'o.id' => $p_id));
        $query = $this->pdo->get($this->table . ' as a');

        return $query->row_array();
    }

    public function get_list_by_order_id($order_id)
    {
        $result = array();
        $this->pdo->select('count(*) as count');
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
        $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
        $this->pdo->where(array('ao.order_id' => $order_id));
        $result['total'] = $this->pdo->count_all_results($this->table . ' as a');

        if (!$result['total']) {
            return $result;
        }

        $this->pdo->select('a.id,a.transaction_date,a.user_id,a.cash,a.credit,ao.order_id');
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
        $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION, BRANCH_TRANSFER));
        $this->pdo->where(array('ao.order_id' => $order_id));
        $this->pdo->order_by('a.id desc');
        $query = $this->pdo->get($this->table . ' as a');
        $result['list'] = $query->result_array();

        return $result;
    }

    public function get_content_by_order_id($order_id)
    {
        $result = array();
        $this->pdo->select('count(*) as count');
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
        $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION));
        $this->pdo->where(array('ao.order_id' => $order_id));
        $this->pdo->group_by('ao.order_id');
        $total = $this->pdo->count_all_results($this->table . ' as a');

        if ($total) {
            $result['total'] = $total;
        } else {
            return false;
        }

        $this->pdo->select('a.id,a.transaction_date,a.user_id,SUM(if(STRCMP(a.type,"I")>0,-(a.cash),a.cash)) as cash,SUM(if(STRCMP(a.type,"I")>0,-(a.credit),a.credit)) as credit,ao.order_id');
        $this->pdo->join('account_orders as ao', 'ao.account_id=a.id');
        $this->pdo->where_not_in('a.account_category_id', array(ADD_COMMISSION));
        $this->pdo->where(array('ao.order_id' => $order_id));
        $this->pdo->order_by('a.id desc');
        $this->pdo->group_by('ao.order_id');
        $query = $this->pdo->get($this->table . ' as a');

        return $query->row_array();
    }

    // 회계 기록은 실제로는 지우지 않도록 한다.
    public function delete($id)
    {
        return $this->pdo->update($this->table, array('enable' => 0, 'updated_at' => $this->now), array('id' => $id));
    }
}
