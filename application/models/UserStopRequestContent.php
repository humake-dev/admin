<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class UserStopRequestContent extends SL_Model
{
    protected $table = 'user_stop_requests';
    protected $accepted_attributes = array('id', 'description', 'enable', 'created_at', 'updated_at');
}
