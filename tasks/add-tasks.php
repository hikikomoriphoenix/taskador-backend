<?php
require_once '../autoload.php';

/**
 * Endpoint for adding to-do tasks to an account. 
 * 
 * Requirements for request:
 * - Must be a POST request
 * - Content-Type = application/json
 * - JSON structure:
 *      <pre><code>
 *      {
 *          "username":<Username of account>,
 *          "token":<Token for authorization>,
 *          "tasks":[
 *              <A task>,
 *              <Another task>,
 *              ...
 *          ]
 *      }   
 *      </code></pre>
 *   
 * Response:   
 * - Content-Type = application/json
 * - On success:
 *      - Status code:
 *          500 - Server error. Retrying the request later might fix the issue.
 *          400 - No input to process.
 *          422 - Can't process request. Username may not exist.
 *          401 - Unauthorized. Either token can't match or expired. Try logging
 *              in to get a new authorization token and retry the request.
 *          405 - Request needs to use POST method   
 *      - JSON structure:
 *          <pre><code>
 *          {}
 *          </code></pre>
 * - On error:
 *      - Status code = 500, 400, 422, 401, or 405
 *      - JSON structure:
 *          <pre><code>
 *          {
 *              "message":<Error message>
 *          }
 *          </code></pre>
 */

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    // Get inputs
    /* @var $input string */
    $input = file_get_contents('php://input');
    if (empty($input)) {
        Response::errorResponse(400, 'Request has no input');
    }    
    /* @var $inputData array */
    $inputData = json_decode($input, true);
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
        Response::errorResponse(401, $e->getMessage());
    } catch (NoTokenException $e) {
        Response::errorResponse(401, $e->getMessage());
    }
    
    if (!$authorized) {
        Response::errorResponse(401, 'unauthorized token');
    }
    
    // Insert tasks to database    
    try {
        Tasks::addTasks($conn, $username, $tasks);
        Response::send(array());
    } catch (Exception $ex) {
        Response::errorResponse(500, $ex->getMessage());
    }
} else {
    Response::errorResponse(405, 'Method is not POST');
}