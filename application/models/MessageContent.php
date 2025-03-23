<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class MessageContent extends SL_Model
{
    protected $table = 'message_contents';
    protected $accepted_attributes = array('id', 'content');
}
