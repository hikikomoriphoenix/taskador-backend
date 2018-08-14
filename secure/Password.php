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
}