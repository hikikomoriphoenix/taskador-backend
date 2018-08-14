<?php
require_once 'autoload.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    /* @var $username string */
    $username = filter_input(INPUT_POST, 'username');
    /* @var $password string */
    $password = filter_input(INPUT_POST, 'password');     

    // Validate username and password. 
    if(!isset($username)) {
        Response::errorResponse(400, 'Username is not set.');
    } else if (!isset($password)) {
        Response::errorResponse(400, 'Password is not set.');
    } else {
        if (!Validate::validateUsername($username)) {
            Response::errorResponse(422, 'Username is invalid.');
        }
        
        if (!Validate::validatePassword($password)) {   
            Response::errorResponse(422, 'Password is invalid.');
        }   
    }

    // Connect to database.
    $conn;
    try {
        $conn = Connect::connectToDB();
    } catch (Exception $ex) {
        Response::errorResponse(500, 'Exception while connecting to database: ' . 
                $ex->getMessage());
    }

    // Check if username is unique.
    if (!usernameIsUnique($conn, $username)) {
        Response::errorResponse(422, 'Username is not unique');
    }

    // Hash password using BCRYPT(CRYPT_BLOWFISH) algorithm. The column to store
    // this value should be CHAR of 60 in length since this algorithm will 
    // always result into 60 characters including the salt generated in the
    // process.
    /* @var $hashedPassword string */
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    if ($hashedPassword ===false) {
        Response::errorResponse(500, 'Failed to hash password');
    }
    
    // Generate token and set expiry date. Set last active date to 'today'.
    /* @var $token string */
    $token = Token::generateToken(32);
    /* @var $expiryDate string */
    $expiryDate = Token::getExpiryDate();
    /* @var $lastActive string */
    $lastActive = date("Y m d");
    
    // Store new account and its fields to database.
    try {
        Account::addNewAccount($conn, $username, $hashedPassword, $token, $expiryDate, 
                $lastActive);
    } catch (Exception $ex) {
        Response::errorResponse(500, 'Exception while adding new account to database: ' .
                $ex->getMessage());         
    }
    
    // return together with an OK status and the generated token.
    $response = ['token' => $token];
    Response::send($response);
}

function usernameIsUnique($conn, $username) {
    $findSameUsername = "SELECT id FROM Accounts WHERE username = '" .
            $username . "'";
    /* @var $countSameUsername int */
    $countSameUsername = $conn->exec($findSameUsername);
    if ($countSameUsername > 0) {
        return false;
    } else {
        return true;
    }
}