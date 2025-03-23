<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class CounselEmployee extends SL_SubModel
{
    protected $table = 'counsel_admins';
    protected $parent_id_name = 'counsel_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('counsel_id', 'admin_id');
}
