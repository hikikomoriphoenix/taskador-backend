<?php
require_once '../autoload.php';
require_once 'ConnectLocal.php';

use PHPUnit\Framework\TestCase;

class TasksTest extends TestCase {
    var $conn;
    
    protected function setUp() {
        $this->conn = ConnectLocal::connectToLocalhostDB();        
    }
    
    public function testAddTasks() {
        $username1 = 'test1';
        $username2 = 'test2';
        
        $tasks1 = [
            'task1',
            'task2',
            'task3'
        ];
        $tasks2 = [
            'task1',
            'task2',
            'task3'
        ];
        
        try {
            Tasks::addTasks($this->conn, $username1, $tasks1);
            Tasks::addTasks($this->conn, $username2, $tasks2);
            $this->assertTrue(true);
        } catch (Exception $ex) {
            $this->fail('Exception on adding tasks: ' . $ex->getMessage());
        }        
    }
}

