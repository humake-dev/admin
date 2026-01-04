<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class BranchPicture extends SL_SubModel
{
    protected $table = 'branch_pictures';
    protected $parent_id_name = 'branch_id';
    protected $accepted_attributes = array('branch_id', 'picture_url', 'enable', 'created_at', 'updated_at');
}
