<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_photo.php';

class Exercise_category_pictures extends SL_photo
{
    protected $model = 'ExerciseCategoryPicture';

    protected function delete_redirect_path($content)
    {
        return 'exercise-categories';
    }
}
