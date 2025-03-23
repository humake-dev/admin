<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class User_groups extends SL
{
    protected $model = 'UserGroup';
    protected $parent_id_name = 'user_id';
}
