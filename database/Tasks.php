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
}

