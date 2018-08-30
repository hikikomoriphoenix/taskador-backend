<?php
class Tasks {
    /**
     * Adds tasks into account.
     * 
     * @param PDO $conn connection to database
     * @param string $username account 
     * @param array $tasks tasks to add
     * @throws PDOException
     */
    static function addTasks(PDO $conn, $username, $tasks) {
        $insert = 'INSERT INTO Tasks_ToDo';
        $params = 'id, username, task';
        $sql = "$insert ($params) VALUES (?, ?, ?);";
        try {
            $st = $conn->prepare($sql);
            foreach ($tasks as $task) {
                $st->bindValue(1, null);
                $st->bindValue(2, $username);
                $st->bindValue(3, $task);
                $st->execute();
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }
 
    /**
     * Get all the tasks under an account with the given username. Each item 
     * contains the task\'s unique id, and the task itself as a string.
     * 
     * @param PDO $conn connection to database
     * @param string $username account username
     * @return array an array of objects containing tasks and its corresponding
     * id.
     * @throws PDOException
     */
    static function getTasks(PDO $conn, $username) {
        $select = "SELECT id, task FROM Tasks_ToDo WHERE username = '$username'";
        try {
            $query = $conn->prepare($select);
            $query->execute();
            $tasks = $query->fetchAll();
            return $tasks;
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
}

