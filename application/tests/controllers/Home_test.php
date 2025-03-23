<?php
/**
 * Part of ci-phpunit-test.
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 *
 * @see       https://github.com/kenjis/ci-phpunit-test
 */
class Home_test extends TestCase
{
    public function test_index()
    {
        //	$this->request->enableHooks();
        $this->request('POST', '/login', ['id' => 'toughjjh', 'password' => 'a12345']);
        $this->request('GET', '/branches/change/1');
        $this->assertStringContainsString('휴메이크', '휴메이크');
    }
}
