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
                . " = $username";
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
            $result = preg_split("/[\s]+/", $taskStringClean);
            
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
}

