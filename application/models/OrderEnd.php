<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class OrderEnd extends SL_SubModel
{
    protected $table = 'order_ends';
    protected $parent_id_name = 'order_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('order_id', 'origin_end_date', 'created_at');
}
