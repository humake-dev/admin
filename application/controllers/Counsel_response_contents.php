<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_content.php';

class Counsel_response_contents extends SL_content
{
    protected $model = 'CounselResponse';
    protected $permission_controller = 'counsels';
    protected $default_view_directory = 'counsel_response_contents';
}
