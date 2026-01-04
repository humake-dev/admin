<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class AccountEditLogField extends SL_SubModel
{
    protected $table = 'account_edit_log_fields';
    protected $parent_id_name = 'account_edit_log_id';
    protected $accepted_attributes = array('account_edit_log_id', 'field', 'origin', 'change');
}
