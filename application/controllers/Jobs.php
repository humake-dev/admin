<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'Basics.php';

class Jobs extends Basics
{
    protected $model = 'Job';
    protected $permission_controller = 'basics';
}
