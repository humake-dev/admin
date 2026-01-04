<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Product_category_sub extends SL_Controller
{
    protected $permission_controller = 'product_categories';

    protected function set_insert_data($data)
    {
        $this->load->model('ProductCategory');
        $data['product_category_id'] = $this->ProductCategory->insert($data);

        return $data;
    }
}
