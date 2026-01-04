<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class NoticeContent extends SL_Model
{
    protected $table = 'notice_contents';
    protected $accepted_attributes = array('id', 'content');
}
