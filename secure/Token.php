<?php
class Token {
    /**
     * Generates a random token of chosen length. This length should be a number
     * that is divisible by 2 because the token is supposedly produced by converting 
     * random bytes into hexadecimals. A byte is equal to 2 hexadecimal digits.
     * 
     * @param Integer $length the expected number of hexadecimal digits. This should 
     * be divisible by two or else DomainException will be thrown.
     * @return String String representation of the generated token, in hexadecimal 
     * format, with a length equal to the given value of $length. 
     * @throws DomainException if value of $length is not divisible by two.
     */
    static function generateToken($length) {
        if ($length % 2 > 0) {
            throw new DomainException("$length should be a number that is divisible"
                    . " by 2");
        }
        $lengthInBytes = $length / 2;
        $randomBytes = random_bytes($lengthInBytes);
        $tokenInHex = bin2hex($randomBytes);
        return "$tokenInHex";
    }

    /**
     * Returns a new expiry date for token. This is always set to 'tomorrow'.
     * 
     * @return String expiry date in "Y m d" string format.
     */
    static function getExpiryDate() {
        $tomorrow = strtotime('tomorrow');
        $expiryDate = date('Y m d', $tomorrow);
        return $expiryDate;
    }
}

