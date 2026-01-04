<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class TempUserTrainer extends SL_SubModel
{
    protected $table = 'temp_user_trainers';
    protected $parent_unique = true;
    protected $parent_id_name = 'temp_user_id';
    protected $accepted_attributes = array('temp_user_id', 'trainer_id', 'created_at', 'updated_at');
}
