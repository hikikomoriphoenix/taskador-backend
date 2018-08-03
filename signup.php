<?php
http_response_code(500);
require_once 'response/response.php';
require_once 'secure/validate.php';
require_once 'database/connect.php';
require_once 'secure/password.php';
require_once 'secure/token.php';

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

    // Apply salt and hash for password.
    
    // Generate token and set expiry date. Set last active date
    
    // Store new account and its fields to database.
    
    // return together with an OK status and the generated token.
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