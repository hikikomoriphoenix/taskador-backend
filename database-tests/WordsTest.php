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
    
    public function testParseTasks() {   
        // Test if entries will include words with apostrophe and hyphen.
        $this->parseTasksWithHyphensAndApostrophes();
        
        // Test if entries will split strings around special characters.
        $this->parseTasksWithSpecialCharacters();
        
        // Test if repeated words are counted
        $this->parseTasksWithRepeatedOccurencies();
    }
    
    private function parseTasksWithHyphensAndApostrophes() {
        $tasks1 = [
            ['task' => "Update my device if it isn't up-to-date."]
        ];
        
        $entries1 = Words::parseTasks($tasks1);
        $expectedEntries1 = [
            ['word' => 'update', 'count' => 1],
            ['word' => 'my', 'count' => 1],
            ['word' => 'device', 'count' => 1],
            ['word' => 'if', 'count' => 1],
            ['word' => 'it', 'count' => 1],
            ['word' => "isn't", 'count' => 1],
            ['word' => 'up-to-date', 'count' => 1]
        ];   
        
        $this->assertThat($entries1, $this->equalTo($expectedEntries1));     
    }
    
    private function parseTasksWithSpecialCharacters() {
        $tasks2 = [
            ['task' => "~1!2@3#4$5%6^7&8*9(0)"]
        ];
        
        $entries2 = Words::parseTasks($tasks2);
        $expectedEntries2 = [
            ['word' => '1', 'count' => 1],
            ['word' => '2', 'count' => 1],
            ['word' => '3', 'count' => 1],
            ['word' => '4', 'count' => 1],
            ['word' => '5', 'count' => 1],
            ['word' => '6', 'count' => 1],
            ['word' => '7', 'count' => 1],
            ['word' => '8', 'count' => 1],
            ['word' => '9', 'count' => 1],
            ['word' => '0', 'count' => 1]
        ];
        
        $this->assertThat($entries2, $this->equalTo($expectedEntries2));        
    }
    
    private function parseTasksWithRepeatedOccurencies() {
        $tasks3 = [
            ['task' => 'cow cow cow mouse mouse cow'],
            ['task' => 'mouse cow mouse cow mouse']
        ];
        
        $entries3 = Words::parseTasks($tasks3);
        $expectedEntries3 = [
            ['word' => 'cow', 'count' => 6],
            ['word' => 'mouse', 'count' => 5]
        ];
        
        $this->assertThat($entries3, $this->equalTo($expectedEntries3));
    }
}

