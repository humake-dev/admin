<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Counsel_contents extends SL_Controller
{
    protected $model = 'CounselContent';
    protected $permission_controller = 'counsels';
}

