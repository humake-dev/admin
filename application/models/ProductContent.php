<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class ProductContent extends SL_Model
{
    protected $table = 'product_contents';
    protected $accepted_attributes = array('product_id', 'content', 'enable', 'created_at', 'updated_at');
}
