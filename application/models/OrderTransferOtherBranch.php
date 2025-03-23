<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class OrderTransferOtherBranch extends SL_SubModel
{
    protected $table = 'order_transfer_other_branches';
    protected $parent_id_name = 'order_transfer_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('order_transfer_id', 'origin_branch_id', 'origin_product_id', 'transfer_branch_id', 'transfer_product_id');
}
