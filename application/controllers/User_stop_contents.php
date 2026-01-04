<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_content.php';

class User_stop_contents extends SL_content
{
    protected $model = 'UserStopContent';
    protected $parent_id_name = 'user_stop_id';
}
