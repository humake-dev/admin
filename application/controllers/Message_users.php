<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Message_users extends SL_Controller
{
    protected $model = 'MessageUser';
    protected $permission_controller = 'messages';

    protected function delete_redirect_path(array $content)
    {
        return '/home/messages/'.$content['user_id'];
    }
}
