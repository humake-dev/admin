<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class AccessControllerIst extends SL_Model
{
    protected $table = 'access_controller_ist';
    protected $accepted_attributes = array('branch_id', 'send_ip', 'dest_ip', 'enable', 'created_at', 'updated_at');
}
