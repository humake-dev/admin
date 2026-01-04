<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class User_model_test extends TestCase
{
    public function setUp():void
    {
		$this->resetInstance();
		$this->CI->load->library('session');
		$this->CI->session->set_userdata(array('branch_id'=>1));
        $this->CI->load->model('User');
        $this->obj = $this->CI->User;
	}
	
	public function test_index()
	{
		$this->obj->user_id=11632;
		$users = $this->obj->get_index();
		$this->assertEquals('1',$enrolls['total']);
		$this->assertCount(1,$enrolls['list']);
	}
	
	public function test_content()
	{
		$enroll = $this->obj->get_content(15319);
		$this->assertEquals('11632',$enroll['user_id']);
	}	
}
