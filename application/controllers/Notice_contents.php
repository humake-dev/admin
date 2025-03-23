<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Notice_contents extends SL_Controller
{
    protected $model = 'NoticeContent';
    protected $permission_controller = 'notices';
}
