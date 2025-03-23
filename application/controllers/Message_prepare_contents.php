<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Message_prepare_contents extends SL_Controller
{
    protected $model = 'MessagePrepareContent';
    protected $permission_controller = 'messages';
}
