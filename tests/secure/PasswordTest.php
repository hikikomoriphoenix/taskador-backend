<?php
require_once __DIR__ . '/../../autoload.php';

use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase {
    public function testHashPassword() {
        $hash = Password::hashPassword('password');
        $this->assertThat(strlen($hash), $this->equalTo(60));
    }
    
    public function testVerifyPassword() {
        $password = 'password';
        $hash = '$2y$10$HZxaCFrGi9xVB9u7LhoevuPDtquuxjpiZbw/wogYHA4TjDG9LM83W';
        $passIsCorrect = Password::verifyPassword($password, $hash);
        $this->assertTrue($passIsCorrect);
    }
}

