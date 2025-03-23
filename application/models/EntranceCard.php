<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_SubModel.php';

class EntranceCard extends SL_SubModel
{
    protected $table = 'entrance_cards';
    protected $parent_id_name = 'entrance_id';
    protected $parent_unique = true;
    protected $accepted_attributes = array('entrance_id', 'facility_card_id');
}
