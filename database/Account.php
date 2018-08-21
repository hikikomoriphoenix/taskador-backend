<?php
// This contains account related functions
class Account {
    /**
     * Stores a new account to the database. Complete well-prepared fields must be 
     * provided for this account.
     * 
     * @param PDO $pdo a PDO instance representing connection to database.
     * @param type $username a unique username.
     * @param type $password a well-hashed password.
     * @param type $token a randomly generated token.
     * @param type $expiryDate expiry date for token.
     * @param type $lastActive date to indicate user's date of last activity.
     */
    static function addNewAccount(PDO $pdo, $username, $password, $token, $expiryDate, 
            $lastActive) {
        $insert = 'INSERT INTO Accounts';
        $params = 'id, username, password, token, expiry_date, last_active';
        $sql = "$insert ($params) VALUES (?, ? ,?, ?, ?, ?);";
        try {
            $st = $pdo->prepare($sql);
            $st->bindValue(1, null);
            $st->bindValue(2, $username);
            $st->bindValue(3, $password);
            $st->bindValue(4, $token);
            $st->bindValue(5, $expiryDate);
            $st->bindValue(6, $lastActive);
            $success = $st->execute();
            if (!$success) {
                throw new Exception('Adding new account failed on call to execute()'
                        );
            }
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
    
    /**
     * Checks the database if username doesn't already exist.
     * 
     * @param PDO $conn a PDO instance representing connection to database.
     * @param String $username username value to check for.
     * @return boolean returns true if username does not exist in the database. 
     * False if it already exist.
     */
    static function usernameIsUnique($conn, $username) {
        $findSameUsername = "SELECT id FROM Accounts WHERE username = '$username'";
        $query = $conn->prepare($findSameUsername);
        $query->execute();
        $results = $query->fetchAll();
        $numResults = count($results);

        if ($numResults > 0) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Get the password of the account with the given username.
     * 
     * @param PDO $conn a PDO instance representing connection to database.
     * @param String $username account username
     * @return mixed the password value of the account with the given username.
     * False if account with the given username does not exist.
     */
    static function getPassword($conn, $username) {
        $getPassword = "SELECT password FROM Accounts WHERE username = "
                . "'$username'";
        $query = $conn->prepare($getPassword);
        $query->execute();
        $results = $query->fetchAll();
        
        if (count($results) > 0) {
            return $results[0];
        } else {
            return false;
        }
    }
}