<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class TempUserFc extends SL_SubModel
{
    protected $table = 'temp_user_fcs';
    protected $parent_unique = true;
    protected $parent_id_name = 'temp_user_id';
    protected $accepted_attributes = array('temp_user_id', 'fc_id', 'created_at', 'updated_at');
}
