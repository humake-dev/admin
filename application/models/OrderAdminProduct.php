<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class OrderAdminProduct extends SL_Model
{
    protected $table = 'order_admin_products';
    protected $accepted_attributes = array('order_admin_id', 'product_id', 'total_price', 'quantity');
}
