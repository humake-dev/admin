<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class OrderTransferContent extends SL_Model
{
    protected $table = 'order_transfer_contents';
    protected $accepted_attributes = array('order_transfer_id', 'content', 'enable', 'created_at', 'updated_at');
}
