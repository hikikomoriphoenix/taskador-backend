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
    
    public function testGetTasks() {
        $username = 'test1';
        
        try {
            $tasks = Tasks::getTasks($this->conn, $username);
            $this->assertThat($tasks[0]['id'], $this->equalTo(1));
            $this->assertThat($tasks[0]['task'], $this->equalTo('task1'));
            $this->assertThat($tasks[2]['id'], $this->equalTo(3));
            $this->assertThat($tasks[2]['task'], $this->equalTo('task3'));
        } catch (Exception $ex) {
            $this->fail('Exception on getting tasks: ' . $ex->getMessage());
        }
    }
}

