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
        // TODO Check expiry date. If not expired return token. Otherwise,
        // generate random token and save. Send response along with the token.
    } else {
        Response::errorResponse(422, 'Username does not exist');
    }
}