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
        $insert = "INSERT INTO Accounts";
        $params = "id, username, password, token, expiry_date, lastActive";
        $sql = "$insert ($params) VALUES ( ?, ? ,?, ?, ?, ?, ? );";
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
                throw new Exception("Adding new account failed on call to execute()"
                        );
            }
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
}