<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Error_report_contents extends SL_Controller
{
    protected $model = 'ErrorReportContent';
    protected $permission_controller = 'users';
}
