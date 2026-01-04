<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_photo.php';

class Product_pictures extends SL_photo
{
    protected $model = 'ProductPicture';
    protected $permission_controller = 'products';
    protected $thumb_array = array('large_thumb' => array('width' => 1080, 'height' => 340), 'medium_thumb' => array('width' => 540, 'height' => 175), 'small_thumb' => array('width' => 275, 'height' => 88));

    protected function delete_redirect_path($content)
    {
        return 'products';
    }
}
