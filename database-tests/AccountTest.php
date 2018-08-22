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
    
    public function testGetPassword() {
        $username = 'test1';
        
        try {
            $password = Account::getPassword($this->conn, $username);
            $this->assertThat($password, $this->equalTo(
                    '$2y$10$HZxaCFrGi9xVB9u7LhoevuPDtquuxjpiZbw/wogYHA4TjDG9LM83W'));
        } catch (Exception $ex) {
            $this->fail('Exception on getting password: ' . $ex->getMessage());
        }
    }
    
    public function testGetToken() {
        $username = 'test1';
        
        try {
            $token = Account::getToken($this->conn, $username);
            $this->assertThat(strlen($token), $this->equalTo(32));
        } catch (Exception $ex) {
            $this->fail('Exception on getting token: ' . $ex->getMessage());
        }
    }
}

