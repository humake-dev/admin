<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class ErrorReportFile extends SL_SubModel
{
    protected $table = 'error_report_files';
    protected $parent_id_name = 'error_report_id';
    protected $accepted_attributes = array('error_report_id', 'file_url', 'enable', 'created_at', 'updated_at');
}
