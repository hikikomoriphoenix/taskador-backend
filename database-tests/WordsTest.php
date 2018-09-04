<?php
require_once '../autoload.php';
require_once 'ConnectLocal.php';

use PHPUnit\Framework\TestCase;

class WordsTest extends TestCase {
    var $conn;
    
    protected function setUp() {
        $this->conn = ConnectLocal::connectToLocalhostDB();        
    }

    public function testGetIdOfLastParsedTask() {
        $username = 'test1';
        
        try {
            $id = Words::getIdOfLastParsedTask($this->conn, $username);
        } catch (Exception $ex) {
            $this->fail('Exception on getting id of last parsed task: '
                    . $ex->getMessage());
        }
        
        $this->assertThat($id, $this->equalTo(9));
    }
}

