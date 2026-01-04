<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Order_contents.php';

class Rent_contents extends Order_contents
{
    protected $permission_controller = 'rents';
}
