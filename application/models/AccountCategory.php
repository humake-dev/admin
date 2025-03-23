<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class AccountCategory extends SL_Model
{
    protected $table = 'account_categories';
    protected $accepted_attributes = array('title', 'enable', 'created_at');
}
