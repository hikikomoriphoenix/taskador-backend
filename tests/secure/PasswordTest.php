<?php
require_once __DIR__ . '/../../autoload.php';

use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase {
    public function testHashPassword() {
        $hash = Password::hashPassword('password');
        $this->assertThat(strlen($hash), $this->equalTo(60));
    }
}

