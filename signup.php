<?php
http_response_code(500);
require_once 'secure/validate.php';
require_once 'database/connect.php';
require_once 'secure/password.php';
require_once 'secure/token.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == "POST") {
    $username = filter_input(INPUT_POST, 'username');
    $password = filter_input(INPUT_POST, 'password');     

    // Validate username and password. 
    if(!isset($username)) {
        http_response_code(400);
        exit(json_encode(array(
            'message' => "Username is not set."
        )));
    } else if (!isset($password)) {
        http_response_code(400);
        exit(json_encode(array(
            'message' => "Password is not set."
        )));
    } else {
        if (!validateUsername($username)) {
            http_response_code(422);
            exit(json_encode(array(
                'message' => "Username is invalid."
                )));
        }
        
        if (!validatePassword($password)) {           
            http_response_code(422);
            exit(json_encode(array(
                'message' => "Password is invalid."
                )));
        }   
    }

    // Connect to database.
    $conn;
    try {
        $conn = connectToDB();
    } catch (Exception $ex) {
        exit("Exception while connecting to database: " . $ex->getMessage());
    }

    // Check if username is unique.
    if (!usernameIsUnique($conn, $username)) {
        http_response_code(422);
        exit("Username is not unique");
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