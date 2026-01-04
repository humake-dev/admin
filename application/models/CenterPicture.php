<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class CenterPicture extends SL_SubModel
{
    protected $table = 'center_pictures';
    protected $parent_id_name = 'center_id';
    protected $accepted_attributes = array('center_id', 'picture_url', 'enable', 'created_at', 'updated_at');
}
