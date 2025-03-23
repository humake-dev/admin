<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Order_stop_logs extends SL_Controller
{
    protected $model = 'OrderStopLog';
    protected $permission_controller = 'users';

    protected function delete_redirect_path(array $content)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return '/home/stops/' . $content['user_id'] . '?tab=3';
        }
    }
}
