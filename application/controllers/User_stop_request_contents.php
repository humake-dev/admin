<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class User_stop_request_contents extends SL_Controller
{
    protected $model = 'UserStopRequestContent';
    protected $permission_controller = 'users';
}
