<?php
require_once '../autoload.php';

/**
 * Endpoint for getting words set to be excluded from top words. These words
 * will be in alphabetical order.
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
 *              "words":[
 *                  <An excluded word>,
 *                  <Another excluded word>
 *                  ...
 *              ]
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
    
    // Get words that are set as excluded    
    try {
        $words = Words::getExcludedWords($conn, $username);
    } catch (Exception $ex) {
        Response::errorResponse(500, 'Exception on getting excluded words: '
                . $ex->getMessage());
    }
    
    $response = ['words' => $words];
    Response::send($response);    
} else {
    Response::errorResponse(405, 'Method is not POST');
}

