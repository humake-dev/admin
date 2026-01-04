<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class User_no_permissions extends SL_Controller
{
    protected $model = '';
    protected $check_permission = false;

    public function index()
    {
        $this->render_format();
    }
}
