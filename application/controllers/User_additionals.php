<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_content.php';

class User_additionals extends SL_content
{
    protected $model = 'UserAdditional';
    protected $parent_id_name = 'user_id';
    protected $default_view_directory = 'user_additionals';
}
