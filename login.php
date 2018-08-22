<?php
require_once 'autoload.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    /* @var $username string */
    $username = filter_input(INPUT_POST, 'username');
    /* @var $password string */
    $password = filter_input(INPUT_POST, 'password');  
    
    $conn = Connect::connectToTaskadorDB();
    
    $correctPassword = Account::getPassword($conn, $username);
    
    if ($correctPassword != false) {
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