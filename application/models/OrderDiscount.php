<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class OrderDiscount extends SL_SubModel
{
    protected $table = 'order_discounts';
    protected $parent_id_name = 'order_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('order_id', 'total_dc_price', 'dc_rate', 'dc_price', 'created_at', 'updated_at');
}
