<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class UserDevice extends SL_SubModel
{
    protected $table = 'user_devices';
    protected $parent_id_name = 'user_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('user_id', 'os', 'token', 'created_at', 'updated_at');
}
