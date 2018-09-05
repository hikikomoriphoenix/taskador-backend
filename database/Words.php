<?php
class Words {
    /**
     * Get the id of a finished task that was last parsed for listing of words
     * used in task statements or phrases.
     * 
     * @param PDO $conn connection to database
     * @param type $username account username
     * @return int id of a finished task
     * @throws Exception
     */
    static function getIdOfLastParsedTask(PDO $conn, $username) {
        $select = 'SELECT id_of_last_parsed_task FROM Accounts WHERE username ='
                . " '$username';";
        try {
            $query = $conn->prepare($select);
            $query->execute();
            $result = $query->fetchAll();
            $id = $result[0]['id_of_last_parsed_task'];
            return $id;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * Set value for id_of_last_parsed_task of account. This helps determine
     * which finished tasks are already parsed for words.
     * 
     * @param PDO $conn connection to database
     * @param type $username account username
     * @param type $id new value for id_of_last_parsed_task
     * @throws PDOException
     */
    static function updateIdOfLastParsedTask(PDO $conn, $username, $id) {
        $update_ = 'UPDATE Accounts';
        $set_ = "SET id_of_last_parsed_task = $id";
        $where_ = "WHERE username = '$username'";
        $sql = "$update_ $set_ $where_;";
        try {
            $st = $conn->prepare($sql);
            $st->execute();            
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
    
    /**
     * Get all finished tasks which has not yet been parsed to add its words to 
     * the list of words used for task statements or phrases.
     * 
     * @param PDO $conn connection to database
     * @param string $username account username
     * @param string $idLastParsed id of task that was last parsed
     * @return string an array with elements containing an 'id' field and a
     * 'task' field
     * @throws PDOException
     */
    static function getUnparsedTasks(PDO $conn, $username, $idLastParsed) {
        $selectFromAccount = 'SELECT id, task FROM Tasks_Finished WHERE username'
                . " = '$username'";
        $unParsed = "AND id > $idLastParsed ORDER BY id ASC";
        $select = "$selectFromAccount $unParsed;";
        
        try {
            $query = $conn->prepare($select);
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
    
    /**
     * Find words in each task. This process ensures no duplicate words are
     * added into the returned array but instead counts every repeated 
     * occurrences.
     * 
     * @param array $tasks an array of tasks to be parsed for words
     * @return array Entries for the words list containing fields named 'word' 
     * for word and 'count' for the number of occurrences for the word. Words 
     * are in lowercase and without any special characters except hyphen and 
     * apostrophe.
     */
    static function parseTasks(array $tasks) {
        $entries = array();
        foreach ($tasks as $task) {
            $taskString = $task['task'];
            
            // Replace special characters excluding hyphen and apostrophe with a
            // white space.
            $taskStringClean = preg_replace("/[^\w\-\']/u", ' ', $taskString);
            
            // Split strings separated by white spaces. Each string represents a
            // word.
            $result = preg_split("/[\s]+/", trim($taskStringClean));
            
            // Make every word have lowercase letters.
            $resultLowerCase = array_map('strtolower', $result);
            
            foreach ($resultLowerCase as $word) {
                // Add the word if it is not yet added. Otherwise, increment 
                // count by 1.
                $words = array_column($entries, 'word');
                $index = array_search($word, $words);
                if($index === false) {
                    $entry['word'] = $word;
                    $entry['count'] = 1;
                    array_push($entries, $entry);
                } else {
                    $entries[$index]['count']++;                    
                }
            }
        }
        return $entries;
    }
    
    /**
     * Add words to list of words used in tasks. If a word already exist in the 
     * list, then update its count.
     * 
     * @param PDO $conn connection to database
     * @param string $username account username
     * @param string $words an array of objects with a 'word' field and a
     * 'count' field
     */
    static function addWordsToList(PDO $conn, $username, $words) {
        try {
            foreach ($words as $word) {
                $wordId = self::getWordID($conn, $username, $word['word']);
                if ($wordId === false) {
                    self::addWordToList($conn, $username, $word['word']);
                } else {
                    self::updateCount($conn, $username, $wordId,
                            $word['count']);
                }
            }
        } catch (PDOException $ex) {
            throw $ex;
        }    
    }
    
    /**
     * Get the id of a given word from account.
     * 
     * @param PDO $conn connection to database
     * @param type $username account username
     * @param type $word word to find
     * @return mixed id of word, false if word does not exist
     * @throws PDOException
     */
    static function getWordID(PDO $conn, $username, $word) {
        $select = "SELECT id FROM Words WHERE username = '$username' AND word"
                . " = '$word';";        
        try {
            $query = $conn->prepare($select);
            $query->execute();
            $result = $query->fetchAll();
            if (empty($result[0]['id'])) {
                return false;
            } else {
                return $result[0]['id'];
            }            
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
    
    /**
     * Add new word to Words table with an initial count of 1.
     * 
     * @param PDO $conn connection to database
     * @param string $username account username
     * @param string $word word to add
     * @throws PDOException
     */
    static function addWordToList(PDO $conn, $username, $word) {
        $insert = 'INSERT INTO Words';
        $params = 'id, username, word, count';
        $sql = "$insert ($params) VALUES (?, ?, ?, ?);";
        try {
            $st = $conn->prepare($sql);
            $st->bindValue(1, null);
            $st->bindValue(2, $username);
            $st->bindValue(3, $word);
            $st->bindValue(4, 1);
            $st->execute();
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
    
    /**
     * Increment a word's count of number of times being used in tasks.
     * 
     * @param PDO $conn connection to database
     * @param string $username account username
     * @param int $id id of word
     * @param int $count amount to increment counts 
     * @throws PDOException
     */
    static function updateCount(PDO $conn, $username, $id, $count) {
        $update = 'UPDATE Words';
        $set = "SET count = count + $count";
        $whereUsername = "WHERE username = '$username'";
        $andId = "AND id = $id";
        $sql = "$update $set $whereUsername $andId;";
        try {
            $st = $conn->prepare($sql);
            $st->execute();
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
}

