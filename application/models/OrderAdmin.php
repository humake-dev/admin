<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order.php';

class OrderAdmin extends Order
{
    protected $table = 'order_admins';
    protected $accepted_attributes = array('admin_id', 'transaction_date', 'payment', 'enable', 'created_at', 'updated_at');

    protected function get_content_data($id)
    {
        $this->pdo->select('p.product_category_id,p.title as product_name,oa.*');
        $this->pdo->join('order_admin_products as oap', 'oap.order_admin_id=oa.id');
        $this->pdo->join('products as p', 'oap.product_id=p.id');
        $this->pdo->where(array('oa.' . $this->p_id => $id));
        $query = $this->pdo->get($this->table . ' as oa');

        return $query->row_array();
    }
}
