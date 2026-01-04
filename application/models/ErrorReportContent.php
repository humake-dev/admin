<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class ErrorReportContent extends SL_Model
{
    protected $table = 'error_report_contents';
    protected $accepted_attributes = array('id', 'content', 'enable', 'created_at', 'updated_at');
}
