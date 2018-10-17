<?php
require_once '../autoload.php';

/**
 * Endpoint for checking if a given username is available and can be registered 
 * for a new account. A username is available if and only if it is unique.
 * 
 * Requirements for request:
 * - Must be a POST request
 * - Content-Type = application/x-www-form-urlencoded or multipart/form-data
 * - Form contains a 'username' field that will be checked for availability
 * 
 * Response:
 * - Content-Type = application/json
 * - On success:
 *      - Status code = 200
 *      - JSON structure:
 *          <pre><code>
 *          {
 *              "available":<true or false>
 *          }
 *          </code></pre>
 * - On error:
 *      - Status code = 500, 400, 422, or 405
 *      - JSON structure:
 *          <pre><code>
 *          {
 *              "message":<Error message>
 *          }
 *          </code></pre>
 */
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