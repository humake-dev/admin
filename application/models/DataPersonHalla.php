<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'IstUser.php';

class DataPersonHalla extends IstUser
{
    protected $connection = 'halla_pdo';
}
