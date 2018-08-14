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
    
    public function testValidatePassword() {
        $test1 = Validate::validatePassword('abcdef');
        $this->assertTrue($test1);
        $test2 = Validate::validatePassword('0000000000000000');
        $this->assertTrue($test2);
        $test3 = Validate::validatePassword('~!@#$%^&*()_+-=');
        $this->assertTrue($test3);
        $test4 = Validate::validatePassword('{}[]:;|<>,.?/`');
        $this->assertTrue($test4);
        $test5 = Validate::validatePassword('12345');
        $this->assertFalse($test5);
        $test6 = Validate::validatePassword('zzzzzzzzzzzzzzzzz');
        $this->assertFalse($test6);
        $test7 = Validate::validatePassword(' leadingspace');
        $this->assertFalse($test7);
        $test8 = Validate::validatePassword('trailingspace ');
        $this->assertFalse($test8);
        $test9 = Validate::validatePassword('mid space');
        $this->assertFalse($test9);
        $test10 = Validate::validatePassword('我不是中國人');
        $this->assertTrue($test10);
    }
}