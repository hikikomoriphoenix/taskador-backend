<?php
require_once '../autoload.php';

/**
 * Endpoint for getting tasks finished during the current week. The response 
 * will include the date finished along with each corresponding finished tasks. 
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
 *          {
 *              "tasks":[
 *                  {"task":<A finished task>, "date_finished":<Date finished>},
 *                  {"task":<Another finished task>, "date_finished":
 *                      <Date finished>},
 *                  ...
 *              ]
 *          }
 *          </code></pre>
 * - On error:
 *      - Status code:
 *          500 - Server error. Retrying the request later might fix the issue.
 *          422 - Can't process request. Username may not exist.
 *          401 - Unauthorized. Either token can't match or expired. Try logging
 *              in to get a new authorization token and retry the request.
 *          405 - Request needs to use POST method  
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
        Response::errorResponse(401, $e->getMessage());
    } catch (NoTokenException $e) {
        Response::errorResponse(401, $e->getMessage());
    }
    
    if (!$authorized) {
        Response::errorResponse(401, 'unauthorized token');
    }
    
    // Query for finished tasks
    try {
        $tasks = Tasks::getFinishedTasks($conn, $username);
        $response = ['tasks' => $tasks];
        Response::send($response);
    } catch (Exception $ex) {
        Response::errorResponse(500, 'Exception on getting finished tasks' . 
                $ex->getMessage());
    }    
} else {
    Response::errorResponse(405, 'Method is not POST');
}