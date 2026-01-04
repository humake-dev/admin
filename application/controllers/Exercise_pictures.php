<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_photo.php';

class Exercise_pictures extends SL_photo
{
    protected $model = 'ExercisePicture';

    protected function delete_redirect_path($content)
    {
        return 'exercises';
    }
}
