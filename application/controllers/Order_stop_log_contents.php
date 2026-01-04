<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order_contents.php';

class Order_stop_log_contents extends Order_contents
{
    protected $model = 'OrderStopLogContent';
    protected $parent_id_name = 'order_stop_log_id';
}
