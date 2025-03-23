<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class CounselUser extends SL_SubModel
{
    protected $table = 'counsel_users';
    protected $parent_id_name = 'counsel_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('counsel_id', 'user_id');
}
