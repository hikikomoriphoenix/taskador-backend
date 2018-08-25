<?php
class Password {
    /**
     * Creates a hash from an input password using BCRYPT(CRYPT_BLOWFISH)
     * algorithm.
     * 
     * @param type $password a validated password input.
     * @return String hashed password containing hash and salt.
     */
    static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    /**
     * Verify if the user's password input is correct.
     * 
     * @param String $password user's input password to verify.
     * @param String $hash hashed password stored in the account.
     * @return Boolean true if password is correct and false if not.
     */
    static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}