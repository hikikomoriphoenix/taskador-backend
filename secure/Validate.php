<?php
class Validate {
    /**
     * Checks if given username is alphanumeric and is at most 16 characters.
     * 
     * @param String $username the username to validate
     * @return Boolean true if username is valid.
     */
    static function validateUsername($username) {
        $result = preg_match("/^[\w]{1,16}$/u", $username);
        if ($result === 1) {
            return true;
        } else if ($result === 0 || $result === false) {
            return false;
        }
    }

    /**
     * Checks if given password does not contain any white space characters and
     * should be at least 6 and at most 16 characters long.
     * 
     * @param String $password the password to validate
     * @return Boolean true if password is valid.
     */
    static function validatePassword($password) {
        $result =  preg_match('/^[\S]{6,16}$/', $password); 
        if ($result === 1) {
            return true;
        } else if ($result === 0 || $result === false) {
            return false;
        }
    }
}

