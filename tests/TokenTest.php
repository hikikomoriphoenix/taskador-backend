<?php
require_once '../secure/Token.php';

use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase {
    public function testGenerateToken() {
        /* @var $token string */
        $token = Token::generateToken(32);
        $this->assertThat(strlen($token), $this->equalTo(32));
    }
}