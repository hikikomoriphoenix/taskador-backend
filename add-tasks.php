<?php
require_once 'autoload.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    // Get inputs
    $input = filter_input(INPUT_POST, 'input');
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
    
    // Check if token is authorized
    
    // Insert tasks to database    
}