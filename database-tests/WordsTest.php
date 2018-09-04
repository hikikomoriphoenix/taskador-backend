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
    
    public function testUpdateIdOfLastParsedTask() {
        $username = 'test1';
        $id = 9;
        
        try {
            Words::updateIdOfLastParsedTask($this->conn, $username, $id);
            $newId = Words::getIdOfLastParsedTask($this->conn, $username);
        } catch (Exception $ex) {
            $this->fail($ex->getMessage());
        }
        
        $this->assertThat($newId, $this->equalTo(9));
    }
    
    public function testGetUnparsedTasks() {
        $username = 'test2';
        
        try {
            $tasks = Words::getUnparsedTasks($this->conn, $username, 10);
        } catch (Exception $ex) {
            $this->fail('Exception on getting unparsed tasks: ' . 
                    $ex->getMessage());
        }
        
        $this->assertThat($tasks[0]['id'], $this->equalTo(13));
        $this->assertThat($tasks[0]['task'], $this->equalTo('task2'));
        $this->assertThat($tasks[1]['id'], $this->equalTo(14));
        $this->assertThat($tasks[1]['task'], $this->equalTo('task3'));
    }
}

