<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class CounselResponse extends SL_Model
{
    protected $table = 'counsel_responses';
    protected $accepted_attributes = array('counsel_id','content', 'enable', 'created_at', 'updated_at');
}
