<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_content.php';

class Temp_user_contents extends SL_content
{
    protected $model = 'TempUserContent';
    protected $parent_id_name = 'temp_user_id';

    protected function delete_redirect_path(array $content)
    {
        if ($this->input->post('return_url')) {
            return $this->input->post('return_url');
        } else {
            return '/temp-users/memo/' . $content['temp_user_id'];
        }
    }
}
