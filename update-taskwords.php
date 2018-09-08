<?php
require_once 'autoload.php';

/**
 * Endpoint for updating the Words table for entries of a given account. The 
 * Words table contains words used in naming tasks. It also tracks how
 * frequently a word is used. In this process, recently finished tasks will be
 * parsed to get each word. New words will be added to the table, while existing
 * ones will be updated for its count. After which, The id of the last task will
 * be saved and the next call to this endpoint will start from the task 
 * following the task of this id.
 * 
 * Requirements for request:
 * - Must be a POST request
 * - Content-Type = application/x-www-form-urlencoded or multipart/form-data
 * - Form contains a 'username' field for account's username
 * - Form contains a 'token' field for token used in authorization
 * 
 * Response:   
 * - Content-Type = application/json
 * - On success:
 *      - Status code = 200
 *      - JSON structure:
 *          <pre><code>
 *          {}
 *          </code></pre>
 * - On error:
 *      - Status code = 500, 400, or 422
 *      - JSON structure:
 *          <pre><code>
 *          {
 *              "message":<Error message>
 *          }
 *          </code></pre>
 */
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

