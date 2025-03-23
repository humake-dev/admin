<?php

class TestCase extends CIPHPUnitTestCase
{
	public function setUp():void
    {
        $this->resetInstance();
        $this->CI->load->library('session');
        $this->CI->session->set_userdata(array('branch_id'=>1));
    }

	public function test_index() {
        $this->CI->load->model('Order');
        //$this->CI->load->model('Enroll');
        echo $this->CI->Order->get_index();
        echo "\n";
        echo $this->CI->Order->get_content();
    }
}
