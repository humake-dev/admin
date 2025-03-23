<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_content.php';

class Product_contents extends SL_content
{
    protected $model = 'ProductContent';
    protected $permission_controller = 'products';
    protected $parent_id_name = 'product_id';
}
