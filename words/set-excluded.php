<?php
require_once '../autoload.php';

/**
 * Endpoint for setting a word as excluded or not excluded from top words.
 * 
 * Requirements for request:
 * - Must be a POST request
 * - Content-Type = application/json
 * - JSON structure:
 *      <pre><code>
 *      {
 *          "username":<username of account>
 *          "token":<token for authorization>
 *          "word":<selected word>
 *          "excluded":<1 if excluded, 0 if not excluded>
 *      }
 *      </code></pre>
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
    
    // Set a selected word as excluded or not excluded in top words    
    try {
        Words::setExcluded($conn, $username, $word, $excluded);
    } catch (Exception $ex) {
        Response::errorResponse(500, "Exception on setting the word's excluded"
                . " value: " . $ex->getMessage());
    }
    
    Response::send(array());
}
