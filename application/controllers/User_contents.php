<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_content.php';

class User_contents extends SL_content
{
    protected $model = 'UserContent';
    protected $parent_id_name = 'user_id';

    protected function delete_redirect_path(array $content)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return '/home/memo/' . $content['user_id'];
        }
    }
}
