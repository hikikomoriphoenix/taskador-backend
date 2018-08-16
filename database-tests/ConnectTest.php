<?php
require_once '../autoload.php';

use PHPUnit\Framework\TestCase;

class ConnectTest extends TestCase {
    public function testConnectToDB() {
        $servername = 'localhost';
        $databasename = 'taskadordb';
        $username = 'taskador';
        $password = 'password';
        try {
            $conn = Connect::connectToDB($servername, $databasename, $username, 
                    $password);     
        } catch (Exception $ex) {
            $this->fail('Exception while connecting to database: ' .
                    $ex->getMessage());
        }        
        $this->assertNotNull($conn);

    }
    
    public function testConnectToDBFailure() {    
        $servername = 'localhost';
        $databasename = 'taskadordb';
        $username = 'taskador';
        $wrongPassword = 'passport';
        try {
            Connect::connectToDB($servername, $databasename, $username,
                    $wrongPassword);
            $this->fail('Connecting to database with wrong password is '
                    . 'supposed to fail');
        } catch (Exception $ex) {
            $this->assertTrue(true);
            $message = $ex->getMessage();
            echo "\n$message";
        }
    }
}

