<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class MessageSender extends SL_SubModel
{
    protected $table = 'message_senders';
    protected $parent_id_name = 'message_id';
    protected $accepted_attributes = array('message_id', 'admin_id', 'phone_number');
}
