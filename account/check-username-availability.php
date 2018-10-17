<?php
require_once '../autoload.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    /* @var $username string */
    $username = filter_input(INPUT_POST, 'username');
    
    if (!isset($username)) {
        Response::errorResponse(400, 'username is not set.');
    }
    
    $valid = Validate::validateUsername($username);    
    if (!$valid) {
        Response::errorResponse(422, 'Invalid username');
    }
    
    $conn = Connect::connectToTaskadorDB();   
   
    try {
        /* @var $available boolean */
        $available = Account::usernameIsUnique($conn, $username);    
    } catch (Exception $ex) {
       Response::errorResponse(500, 'Exception on checking if username is unique'
               . $ex->getMessage()); 
    }   
    $response = ['available' => $available];   
    Response::send($response);
} else {
    Response::errorResponse(405, 'Method is not POST');
}