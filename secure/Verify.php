<?php
class Verify {    
    /**
     * Verify if the given token is authorized to make requests. 
     * 
     * @param PDO $conn connection to database
     * @param string $username account username
     * @param string $token token to verify
     * @return boolean true if verified that token is correct, false otherwise
     * @throws GetExpiryDateAndTokenFailureException
     * @throws NoAccountException
     * @throws ExpiredTokenException
     * @throws NoTokenException
     */
    static function verifyToken($conn, $username, $token) {
        try {
            $expiryDateAndToken = self::getExpiryDateAndToken($conn, $username);
        } catch (GetExpiryDateAndTokenFailureException $gedatfe) {
            throw $gedatfe;
        } catch (NoAccountException $nae) {
            throw $nae;
        }      

        $accountToken = $expiryDateAndToken['token'];
        $expiryDate = $expiryDateAndToken['expiry_date'];
        
        if (strtotime('today') >= strtotime($expiryDate)) {   
            throw new ExpiredTokenException('Submitted token has already expired.');
        }

        if (empty($accountToken)) {
            throw new NoTokenException('No valid token available.');
        }

        if ($token == $accountToken) {
            return true;
        } else {
            return false;
        }        
    }
    
    private static function getExpiryDateAndToken($conn, $username) {
        try {
            $expiryDateAndToken = Account::getExpiryDateAndToken($conn, $username);
        } catch (PDOException $e) {
            throw new GetExpiryDateAndTokenFailureException(
                    'Exception on getting expiry date and ' .
                    'token: ' . $e->getMessage());
        }

        if (empty($expiryDateAndToken)) {
            throw new NoAccountException('No values for token and expiry date found'
                    . '. Account may not exist.');
        }
        
        return $expiryDateAndToken;
    }
}

class NoAccountException extends Exception {}

class ExpiredTokenException extends Exception {}

class NoTokenException extends Exception {}

class GetExpiryDateAndTokenFailureException extends Exception {}