<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class CounselEditLogField extends SL_SubModel
{
    protected $table = 'counsel_edit_log_fields';
    protected $parent_id_name = 'counsel_edit_log_id';
    protected $accepted_attributes = array('counsel_edit_log_id', 'field', 'origin', 'change');
}
