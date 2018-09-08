<?php
require_once 'autoload.php';

/**
 * Endpoint for simply verifying a token's authorization. If the token is not
 * verified to be authorized, then the user needs to login using the account's
 * password to get a new fresh token.
 * 
 * Requirements for request:
 * - Must be a POST request
 * - Content-Type = application/x-www-form-urlencoded or multipart/form-data
 * - Form contains a 'username' field for account's username
 * - Form contains a 'token' field for the token to be verified
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
    
    try {
        $conn = Connect::connectToTaskadorDB();
    } catch (PDOException $e) {
        Response::errorResponse(500, 'Exception on connecting to database: ' . 
                $e->getMessage());
    }
    
    try {
        $verified = Verify::verifyToken($conn, $username, $token);
    } catch (GetExpiryDateAndTokenFailureException $e) {
        Response::errorResponse(500, $e->getMessage());
    } catch (NoAccountException $e) {
        Response::errorResponse(422, $e->getMessage());
    } catch (ExpiredTokenException $e) {
        Response::errorResponse(422, $e->getMessage());
    } catch (NoTokenException $e) {
        Response::errorResponse(500, $e->getMessage());
    }    
    
    if ($verified) {
        Response::send(array());
    } else {
        Response::errorResponse(422, 'Submitted token is not correct.');
    }
}