<?php
require_once 'autoload.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    /* @var $username string */
    $username = filter_input(INPUT_POST, 'username');
    /* @var $password string */
    $password = filter_input(INPUT_POST, 'password');  
    
    $conn = Connect::connectToTaskadorDB();
    
    // Get the hashed password from the account. This will be used to verify if
    // the input password is correct.
    $correctPassword = Account::getPassword($conn, $username);
    
    if ($correctPassword != false) {
        // If the input password is verified to be correct, then try to send the
        // auth token. The getToken function checks if the token is expired and
        // will generate a new one if it is.
        if (Password::verifyPassword($password, $correctPassword)) {
            try {
                $token = Account::getToken($conn, $username);
            } catch (PDOException $ex) {
                Response::errorResponse(500, 'Exception while getting token: '
                        . $ex->getMessage());
            }
            
            if ($token == false) {
                Response::errorResponse(422, 'Username does not exist');
            }
            
            $response = ['token' => $token];
            Response::send($response);
        } else {
            Response::errorResponse(422, 'Incorrect password.');
        }
    } else {
        Response::errorResponse(422, 'Username does not exist');
    }
}