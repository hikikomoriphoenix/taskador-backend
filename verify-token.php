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
        $verified = Verify::verifyToken($conn, $username, $token);
    } catch (GetExpiryDateAndTokenFailureException $e) {
        Response::errorResponse(500, $e->getMessage());
    } catch (NoAccountException $e) {
        Response::errorResponse(422, $e->getMessage());
    } catch (ExpiredTokenException $e) {
        Response::errorResponse(422, $e->getMessage());
    } catch (NoTokenException $e) {
        Response::errorResponse(500, $e->getMessage());
    }    
    
    if ($verified) {
        Response::send(array());
    } else {
        Response::errorResponse(422, 'Submitted token is not correct.');
    }
}