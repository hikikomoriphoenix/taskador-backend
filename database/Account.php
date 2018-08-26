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
     * 
     * @throws PDOException
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
     * 
     * @throws PDOException
     */
    static function getPassword($conn, $username) {
        try {
            $getPassword = "SELECT password FROM Accounts WHERE username = "
                    . "'$username'";
            $query = $conn->prepare($getPassword);
            $query->execute();
            $results = $query->fetchAll();

            if (count($results) > 0) {
                return $results[0]['password'];
            } else {
                return false;
            }
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
    
    /**
     * Get token from account. If token is expired, a new token is generated.
     * 
     * @param PDO $conn a PDO instance representing connection to database.
     * @param String $username account username
     * @return Mixed account token or false if account with given username is
     * not found.
     * 
     * @throws PDOException
     */
    static function getToken($conn, $username) {
        try {
            $results = self::getExpiryDateAndToken($conn, $username);

            if (!empty($results)) {
                $token = $results['token'];
                $expiryDate = $results['expiry_date'];

                return self::freshifyToken($conn, $username, $token, $expiryDate);
            } else {
                return false;
            }
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
    
    /**
     * Get the account token stored in database and its expiry date.
     * 
     * @param PDO $conn connection to database
     * @param String $username account username
     * @return Array an array containing values for token and expiry date with 
     * corresponding keys, "token" and "expiry_date" respectively. Empty if 
     * query didn't return results or username doesn't exist.
     * @throws PDOException
     */
    static function getExpiryDateAndToken($conn, $username) {
        try {
            $getExpiryDateAndToken = "SELECT token, expiry_date FROM Accounts WHERE"
                        . " username = '$username'";
            $query = $conn->prepare($getExpiryDateAndToken);
            $query->execute();
            $results = $query->fetchAll();  
            return $results[0];
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
    
    /**
     * Check if current date is less than expiry date. If otherwise, then token
     * is already expired and a new token should be generated.
     * 
     * @param PDO $conn connection to database.
     * @param String $username account username.
     * @param String $token account token.
     * @param String $expiryDate token's expiry date.
     * @return String current token if not expired or new generated token if 
     * expired.
     * 
     * @throws PDOException
     */
    private static function freshifyToken($conn, $username, $token, $expiryDate){
        try {
            if (strtotime('today') < strtotime($expiryDate)) {
                return $token;
            } else {
                return self::updateToken($conn, $username);
            }
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
    
    /**
     * Generate new token with a new expiry date. Update account with these new 
     * values.
     * 
     * @param type $conn a PDO instance representing connection to database.
     * @param type $username account username
     * @return String a new generated token.
     * 
     * @throws PDOException
     */
    private static function updateToken($conn, $username) {
        try {
            $token = Token::generateToken(32);
            $expiryDate = Token::getExpiryDate();

            $updateTokenAndExpiryDate = "UPDATE Accounts SET token = ?, "
                            . "expiry_date = ? WHERE username = '$username'";
            $update = $conn->prepare($updateTokenAndExpiryDate);
            $update->bindValue(1, $token);
            $update->bindValue(2, $expiryDate);
            $update->execute();

            return $token;
        } catch (PDOException $ex) {
            throw $ex;
        }
    }
}