<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class OrderStopLogContent extends SL_Model
{
    protected $table = 'order_stop_log_contents';
    protected $accepted_attributes = array('order_stop_log_id', 'content', 'enable', 'created_at', 'updated_at');
}
