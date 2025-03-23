<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class ProductCategoryContent extends SL_Model
{
    protected $table = 'product_category_contents';
    protected $accepted_attributes = array('product_category_id', 'content', 'enable', 'created_at', 'updated_at');
}
