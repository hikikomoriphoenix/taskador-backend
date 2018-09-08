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
        $username1 = 'test2';        
        $username2 = 'test3';
        
        try {
            $tasks1 = Words::getUnparsedTasks($this->conn, $username1, 10);
            $tasks2 = Words::getUnparsedTasks($this->conn, $username2, null);
        } catch (Exception $ex) {
            $this->fail('Exception on getting unparsed tasks: ' . 
                    $ex->getMessage());
        }
        
        $this->assertThat($tasks1[0]['id'], $this->equalTo(13));
        $this->assertThat($tasks1[0]['task'], $this->equalTo('task2'));
        $this->assertThat($tasks1[1]['id'], $this->equalTo(14));
        $this->assertThat($tasks1[1]['task'], $this->equalTo('task3'));  
        
        $this->assertThat($tasks2[0]['id'], $this->equalTo(11));    
        $this->assertThat($tasks2[0]['task'], $this->equalTo('task1'));  
        $this->assertThat($tasks2[1]['id'], $this->equalTo(12));  
        $this->assertThat($tasks2[1]['task'], $this->equalTo('task2'));  
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
    
    public function testGetWordID() {
        $username = 'test1';
        $word = 'buy';        
        $nonexistentWord = 'sdfklas';
        
        try {
            $id1 = Words::getWordID($this->conn, $username, $word);
            $id2 = Words::getWordID($this->conn, $username, $nonexistentWord);
        } catch (Exception $ex) {
            $this->fail('Exception on getting id for word' . $ex->getMessage());
        }
        
        $this->assertThat($id1, $this->equalTo(1));
        $this->assertThat($id2, $this->equalTo(false));
    }
    
    public function testAddWordToList() {
        $username = 'test1';
        $word = 'watch';
        $count = 3;
        
        try {
            Words::addWordToList($this->conn, $username, $word, $count);
            $id = Words::getWordID($this->conn, $username, $word);
        } catch (Exception $ex) {
            $this->fail($ex->getMessage());            
        }
        
        $this->assertNotFalse($id);
    }
    
    public function testUpdateCount() {
        $username = 'test1';
        $id = 1;
        $count = 2;
        
        try {
            Words::updateCount($this->conn, $username, $id, $count);
            $this->assertTrue(true);
            // Check the Words table if the result is correct.
        } catch (Exception $ex) {
            $this->fail('Exception on updating count: ' . $ex->getMessage());
        }
    }
    
    public function testAddWordsToList() {
        $username = 'test1';
        $words = [
            ['word' => 'buy', 'count' => 2],
            ['word' => 'exercise', 'count' => 5],
            ['word' => 'clean', 'count' => 7]
        ];
        
        try {
            Words::addWordsToList($this->conn, $username, $words);
            $id1 = Words::getWordID($this->conn, $username, 'exercise');
            $id2 = Words::getWordID($this->conn, $username, 'clean');
        } catch (Exception $ex) {
            $this->fail($ex->getMessage());
        }
        
        $this->assertNotFalse($id1);
        $this->assertNotFalse($id2);
    }
    
    public function testGetTopWords() {
        $username = 'test2';
        $numResults = 5;
        
        try {
            $topWords = Words::getTopWords($this->conn, $username, $numResults);
        } catch (Exception $ex) {
            $this->fail('Exception on getting top words: ' . $ex->getMessage());
        }
        
        $expectedTopWords = [
            ['word' => 'practice', 'count' => '67'],
            ['word' => 'watch', 'count' => '32'],
            ['word' => 'buy', 'count' => '25'],
            ['word' => 'write', 'count' => '21'],
            ['word' => 'hangout', 'count' => '18']
        ];
        
        $this->assertThat($topWords, $this->equalTo($expectedTopWords));
    }
    
    public function testSetExcluded() {
        $username = 'test1';
        $word = 'buy';
        $excluded = 1;
        //$excluded = 0;
        
        try {
            Words::setExcluded($this->conn, $username, $word, $excluded);
            $this->assertTrue(true);
            // Check the value in table.
        } catch (Exception $ex) {
            $this->fail("Exception on setting a word's excluded value: " . 
                    $ex->getMessage());
        }
    }
    
    public function testGetExcludedWords() {
        $username = 'test3';
        
        try {
            $words = Words::getExcludedWords($this->conn, $username);
        } catch (Exception $ex) {
            $this->fail('Exception on getting excluded words: ' . 
                    $ex->getMessage());
        }
        
        $expectedWords = ['eat', 'fight', 'hunt'];
        
        $this->assertThat($words, $this->equalTo($expectedWords));
    }
}

