<?php
require_once 'autoload.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $username = filter_input(INPUT_POST, 'username');
    $token = filter_input(INPUT_POST, 'token');
    
    try {
        $conn = Connect::connectToTaskadorDB();
    } catch (PDOException $e) {
        Response::errorResponse(500, 'Exception on connecting to database: ' . 
                $e->getMessage());
    }
    
    try {
        $expiryDateAndToken = Account::getExpiryDateAndToken($conn, $username);
    } catch (PDOException $e) {
        Response::errorResponse(500, 'Exception on getting expiry date and ' .
                'token: ' . $e->getMessage());
    }
    
    if (empty($expiryDateAndToken)) {
        Response::errorResponse(422, 'No values for token and expiry date found'
                . '. Account may not exist.' );
    }
    
    $accountToken = $expiryDateAndToken['token'];
    $expiryDate = $expiryDateAndToken['expiry_date'];
    
    if (strtotime('today') >= strtotime($expiryDate)) {
        Response::errorResponse(422, 'Submitted token has already expired.');
    }
    
    if (empty($accountToken)) {
        Response::errorResponse(500, 'No valid token available.');
    }
    
    $response = ['token' => $accountToken];
    Response::send($response);
}