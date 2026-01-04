<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Rent_model_limit_test extends TestCase
{
	public function setUp():void
    {
		$this->resetInstance();
		$this->CI->load->library('session');
		$this->CI->session->set_userdata(array('branch_id'=>1));
        $this->CI->load->model('Rent');
        $this->obj = $this->CI->Rent;
    }

	public function test_index()
	{
		
	}
}
