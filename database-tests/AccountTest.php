<?php
require_once '../autoload.php';
require_once 'ConnectLocal.php';

use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase {
    public function testUsernameIsUnique() {
        $conn = ConnectLocal::connectToLocalhostDB();
       
        $username = 'test1'; //should be a unique username        
        $unique = Account::usernameIsUnique($conn, $username);   
        $this->assertTrue($unique, 'Given username already exists');
    }
}

