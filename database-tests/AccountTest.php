<?php
require_once '../autoload.php';
require_once 'ConnectLocal.php';

use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase {
    var $conn;
    
    protected function setUp() {
        $this->conn = ConnectLocal::connectToLocalhostDB();        
    }
    
    public function testAddNewAccount() {
        $username = 'test3'; //should be a unique username
        $password = Password::hashPassword('password');
        $token = Token::generateToken(32);
        $expiryDate = Token::getExpiryDate();
        $lastActive = date("Y-m-d");
        
        try {
            Account::addNewAccount($this->conn, $username, $password, $token, 
                    $expiryDate, $lastActive); 
            $this->assertTrue(true);
        } catch (Exception $ex) {
            $this->fail('Exception while adding account: ' . $ex->getMessage());
        }       
    }
    public function testUsernameIsUnique() {       
        $username = 'test4'; //should be a unique username        
        $unique = Account::usernameIsUnique($this->conn, $username);   
        $this->assertTrue($unique, 'Given username already exists');
    }
}

