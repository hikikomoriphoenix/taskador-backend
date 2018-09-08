<?php
require_once 'autoload.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    // Get inputs
    /* @var $input string */
    $input = file_get_contents('php://input');
    if (empty($input)) {
        Response::errorResponse(400, 'Request has no input');
    }    
    /* @var $inputData array */
    $inputData = json_decode($input);
    /* @var $username string */
    $username = $inputData['username'];
    /* @var $token string */
    $token = $inputData['token'];
    /* @var $tasks array */
    $tasks = $inputData['tasks'];
   
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
    
    // Save finished tasks to Tasks_Finished table
    try {
        Tasks::saveFinishedTasks($conn, $username, $tasks);
    } catch (Exception $ex) {
        Response::errorResponse(500, 'Exception on saving finished tasks: ' . 
                $ex->getMessage());
    }
    
    // Remove finished tasks from Tasks_ToDo table
    try {
        Tasks::deleteTasks($conn, $username, $tasks);
    } catch (Exception $ex) {
        Response::errorResponse(500, 'Exception on deleting finished tasks: ' .
                $ex->getMessage());
    }
    
    Response::send(array());
}

