<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class UserAccessCard extends SL_SubModel
{
    protected $table = 'user_access_cards';
    protected $parent_id_name = 'user_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('user_id', 'card_no', 'enable', 'created_at', 'updated_at');
}
