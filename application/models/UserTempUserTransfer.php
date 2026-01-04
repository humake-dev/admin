<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class UserTempUserTransfer extends SL_SubModel
{
    protected $table = 'user_temp_user_transfers';
    protected $parent_id_name = 'user_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('user_id', 'temp_user_id', 'created_at', 'updated_at');
}
