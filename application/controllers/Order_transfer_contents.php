<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order_contents.php';

class Order_transfer_contents extends Order_contents
{
    protected $model = 'OrderTransferContent';
    protected $parent_id_name = 'order_transfer_id';
}
