<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class UserUserTransfer extends SL_Model
{
    protected $table = 'user_user_transfers';
    protected $accepted_attributes = array('user_id', 'user_transfer_id');
}
