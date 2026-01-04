<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL.php';

class Devices extends SL_Controller
{
    protected $script = 'devices/index.js';
    protected $permission_controller = 'users';

    public function webcam()
    {
        $this->layout->add_css('bootstrap.min.css');
        $this->layout->add_js('devices/webcam.js?version=' . $this->assets_version);
        $this->layout->layout = 'device';
        $this->layout->render($this->router->fetch_class() . '/' . $this->router->fetch_method(), $this->return_data);
    }

    public function session()
    {
        echo $this->session->userdata('branch_id');
    }

    protected function render_default_resource()
    {
        $this->layout->add_js('jquery.min.js');
    }
}
