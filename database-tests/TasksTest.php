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
    
    public function testSaveFinishedTasks() {
        $username = 'test1';
        
        $tasks = [
            ['task' => 'task1', 'id' => 1],
            ['task' => 'task2', 'id' => 2],
            ['task' => 'task3', 'id' => 3]
        ];
        
        try {
            Tasks::saveFinishedTasks($this->conn, $username, $tasks);
            $this->assertTrue(true);
        } catch (Exception $ex) {
            $this->fail('Exception on saving finished tasks: ' . 
                    $ex->getMessage());
        }
    }
    
    public function testDeleteTasks() {
        $username1 = 'test1';
        $username2 = 'test2';
        
        $tasks1 = [
            ['tasks' => 'task1', 'id' => 1],
            ['tasks' => 'task2', 'id' => 2],
            ['tasks' => 'task3', 'id' => 3],
        ];
        
        $tasks2 = [
            ['tasks' => 'task1', 'id' => 4],
            ['tasks' => 'task2', 'id' => 5],
            ['tasks' => 'task3', 'id' => 6],
        ];
        
        try {
            Tasks::deleteTasks($this->conn, $username1, $tasks1);
            Tasks::deleteTasks($this->conn, $username2, $tasks2);
            $this->assertTrue(true);
        } catch (Exception $ex) {
            $this->fail('Exception on deleting tasks: ' . $ex->getMessage());
        }
    }
}

