<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_file.php';

class Error_report_files extends SL_file
{
    protected $model = 'ErrorReportFile';
    protected $permission_controller = 'users';

    protected function delete_redirect_path($content)
    {
        return 'error-reports';
    }
}
