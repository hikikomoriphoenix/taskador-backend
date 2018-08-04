<?php
http_response_code(500);
require_once 'response/response.php';
require_once 'secure/validate.php';
require_once 'database/connect.php';
require_once 'secure/password.php';
require_once 'secure/token.php';
require_once 'database/account.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == "POST") {
    $username = filter_input(INPUT_POST, 'username');
    $password = filter_input(INPUT_POST, 'password');     

    // Validate username and password. 
    if(!isset($username)) {
        errorResponse(400, "Username is not set.");
    } else if (!isset($password)) {
        errorResponse(400, "Password is not set.");
    } else {
        if (!validateUsername($username)) {
            errorResponse(422, "Username is invalid.");
        }
        
        if (!validatePassword($password)) {   
            errorResponse(422, "Password is invalid.");;
        }   
    }

    // Connect to database.
    $conn;
    try {
        $conn = connectToDB();
    } catch (Exception $ex) {
        errorResponse(500, "Exception while connecting to database: " . 
                $ex->getMessage());
    }

    // Check if username is unique.
    if (!usernameIsUnique($conn, $username)) {
        errorResponse(422, "Username is not unique");
    }

    // Hash password using BCRYPT(CRYPT_BLOWFISH) algorithm. The column to store
    // this value should be CHAR of 60 in length since this algorithm will 
    // always result into 60 characters including the salt generated in the
    // process.
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Generate token and set expiry date. Set last active date to 'today'.
    $token = generateToken(32);
    $expiryDate = getExpiryDate();
    $lastActive = date("Y m d");
    
    // Store new account and its fields to database.
    try {
        addNewAccount($conn, $username, $hashedPassword, $token, $expiryDate, 
                $lastActive);
    } catch (Exception $ex) {
        errorResponse(500, "Exception while adding new account to database: " .
                $ex->getMessage());         
    }
    
    // return together with an OK status and the generated token.
    $response = array('token' => $token);
    send($response);
}

function usernameIsUnique($conn, $username) {
    $findSameUsername = "SELECT id FROM Accounts WHERE username = '" .
            $username . "'";
    $countSameUsername = $conn->exec($findSameUsername);
    if ($countSameUsername > 0) {
        return false;
    } else {
        return true;
    }
}