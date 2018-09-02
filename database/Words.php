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
}

