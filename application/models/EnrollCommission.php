<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class EnrollCommission extends SL_SubModel
{
    protected $table = 'enroll_commissions';
    protected $parent_id_name = 'enroll_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('enroll_id', 'commission', 'created_at', 'updated_at');
}
