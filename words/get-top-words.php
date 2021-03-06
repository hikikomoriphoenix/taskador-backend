<?php
require_once '../autoload.php';

/**
 * Endpoint for getting words most frequently used in tasks. Words are ordered
 * starting from the word with most counts. Words that are set to be excluded
 * from top words are not included in the results. It is recommended that the 
 * Words table be updated, by calling the update-taskwords endpoint, before
 * calling this one. 
 * 
 * Requirements for request:
 * - Must be a POST request
 * - Content-Type = application/json
 * - JSON structure:
 *      <pre><code>
 *      {
 *          "username":<username of account>,
 *          "token":<token for authorization>,
 *          "number_of_results":<expected number of results>
 *      }
 *      </code></pre>
 * 
 * Response:
 * - Content-type = application/json
 * - On success:
 *      - Status code = 200
 *      - JSON structure:
 *          <pre><code>
 *          {
 *              "top_words":[
 *                  {"word":<top word>, "count":<times used in tasks>},
 *                  {"word":<second top word>, "count":<times used in tasks>},
 *                  ...
 *              ]
 *          }
 * - On error:
 *      - Status code:
 *          500 - Server error. Retrying the request later might fix the issue.
 *          400 - No input to process.
 *          422 - Can't process request. Username may not exist.
 *          401 - Unauthorized. Either token can't match or expired. Try logging
 *              in to get a new authorization token and retry the request.
 *          405 - Request needs to use POST method 
 *      - JSON structure
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
    /* @var $numResults int */
    $numResults = $inputData['number_of_results'];
   
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
    
    // Get top words from Words table
    try {
        $topWords = Words::getTopWords($conn, $username, $numResults);
    } catch (Exception $ex) {
        Response::errorResponse(500, 'Exception on getting top words: ' . 
                $ex->getMessage());
    }
    
    $response = [ 'top_words' => $topWords];
    Response::send($response);
} else {
    Response::errorResponse(405, 'Method is not POST');
}
