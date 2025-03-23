<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class MessagePrepareAdmin extends SL_SubModel
{
    protected $table = 'message_prepare_admins';
    protected $parent_id_name = 'message_prepare_id';
    protected $accepted_attributes = array('message_prepare_id', 'admin_id');
}
