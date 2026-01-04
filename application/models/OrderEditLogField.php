<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class OrderEditLogField extends SL_SubModel
{
    protected $table = 'order_edit_log_fields';
    protected $parent_id_name = 'order_edit_log_id';
    protected $accepted_attributes = array('order_edit_log_id', 'field', 'origin', 'change');
}
