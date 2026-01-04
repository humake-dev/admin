<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class ExtraConnModel extends SL_Model
{
    protected $connection;
  
    public function __construct()
    {
        $this->pdo = $this->load->database($this->connection, true);
        $this->timezone = new DateTimeZone($this->config->item('time_reference'));

        $date_time_obj = new DateTime('now', $this->timezone);
        $this->now = $date_time_obj->format('Y-m-d H:i:s');
        $this->today = $date_time_obj->format('Y-m-d');
    }
}
