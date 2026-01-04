<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class UserStopContent extends SL_Model
{
    protected $table = 'user_stop_contents';
    protected $accepted_attributes = array('user_stop_id', 'content', 'enable', 'created_at', 'updated_at');
}
