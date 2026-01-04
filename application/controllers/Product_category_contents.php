<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_content.php';

class Product_category_contents extends SL_content
{
    protected $model = 'ProductCategoryContent';
    protected $permission_controller = 'product_categories';
    protected $parent_id_name = 'product_category_id';
}
