<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class AdminLoginLog extends SL_Model
{
    protected $table = 'admin_login_logs';
    protected $accepted_attributes = array('admin_id', 'ip', 'created_at');
}
