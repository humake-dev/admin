<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class EmployeeAccessCard extends SL_SubModel
{
    protected $table = 'admin_access_cards';
    protected $parent_id_name = 'admin_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('admin_id', 'card_no', 'enable', 'created_at', 'updated_at');
}
