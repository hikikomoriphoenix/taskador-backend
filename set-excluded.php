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
    /* @var $word string */
    $word = $inputData['word'];
    /* @var $excluded bool */
    $excluded = $inputData['excluded'];
   
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
    
    // Set a selected as excluded or not excluded in top words    
}

