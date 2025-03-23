<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class OrderBlock extends SL_Model
{
    protected $table = 'order_blocks';
    protected $accepted_attributes = array('product_id', 'user_count', 'reference_date','transaction_date', 'period', 'enable', 'created_at');
}
