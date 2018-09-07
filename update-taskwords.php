<?php
require_once 'autoload.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $username = filter_input(INPUT_POST, 'username');
    $token = filter_input(INPUT_POST, 'token');
    
    // Connect to database
    try {
        $conn = Connect::connectToTaskadorDB();
    } catch (PDOException $e) {
        Response::errorResponse(500, 'Exception on connecting to database: ' . 
                $e->getMessage());
    }    
    
    // Check if token is authorized
    try {
        $authorized = Verify::verifyToken($conn, $username, $token);
    } catch (GetExpiryDateAndTokenFailureException $e) {
        Response::errorResponse(500, $e->getMessage());
    } catch (NoAccountException $e) {
        Response::errorResponse(422, $e->getMessage());
    } catch (ExpiredTokenException $e) {
        Response::errorResponse(422, $e->getMessage());
    } catch (NoTokenException $e) {
        Response::errorResponse(500, $e->getMessage());
    }
    
    if (!$authorized) {
        Response::errorResponse(422, 'unauthorized token');
    }
    
    // Update the list of words used in tasks
    try {
        $idOfLastParsedTask = Words::getIdOfLastParsedTask($conn, $username);
        $unparsedTasks = Words::getUnparsedTasks($conn, $username,
                $idOfLastParsedTask);
        if (!empty($unparsedTasks)) {
            $words = Words::parseTasks($unparsedTasks);   
            Words::addWordsToList($conn, $username, $words);

            // Set the new id for last parsed task
            $lastTaskIndex = count($unparsedTasks) - 1;
            $newIdOfLastParsedTask = $unparsedTasks[$lastTaskIndex]['id'];                
            Words::updateIdOfLastParsedTask($conn, $username, $idOfLastParsedTask);
        }
    } catch (Exception $ex) {
        Response::errorResponse(500, 'Exception on updating words used in task:'
                . ' ' . $ex->getMessage());        
    }
    
    Response::send(array());
}

