<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_content.php';

class Order_contents extends SL_content
{
    protected $model = 'OrderContent';
    protected $permission_controller = 'orders';
    protected $parent_id_name = 'order_id';

    protected function delete_redirect_path(array $content)
    {
        return '/home/memo/'.$content['user_id'];
    }
}
