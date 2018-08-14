<?php
require_once __DIR__ . '/../../autoload.php';

use PHPUnit\Framework\TestCase;

class ValidateTest extends TestCase {
    public function testValidateUsername() {
        $test1 = Validate::validateUsername('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
        $this->assertFalse($test1);
        $test2 = Validate::validateUsername('v4l1dn4m3');
        $this->assertTrue($test2);
        $test3 = Validate::validateUsername('_');
        $this->assertTrue($test3);
        $test4 = Validate::validateUsername('2222222222222222');
        $this->assertTrue($test4);
        $test5 = Validate::validateUsername('');
        $this->assertFalse($test5);
        $test6 = Validate::validateUsername('the name');
        $this->assertFalse($test6);
        $test7 = Validate::validateUsername('^&&^*$@#');
        $this->assertFalse($test7);
        $test8 = Validate::validateUsername('程序員');
        $this->assertTrue($test8);
    }
}