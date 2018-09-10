<?php
require_once '../autoload.php';

/**
 * Endpoint for logging in to account. A successful login will return a token 
 * which will be used for authorization to allow a user to make requests and
 * access tasks and other data in his/her account.
 * 
 * Requirements for request:
 * - Must be a POST request
 * - Content-Type = application/x-www-form-urlencoded or multipart/form-data
 * - Form contains a 'username' field for account's username
 * - Form contains a 'password' field for account's password
 * 
 * Response:
 * - Content-Type = application/json
 * - On success:
 *      - Status code = 200
 *      - JSON structure:
 *          <pre><code>
 *          {
 *              "token":<Token to be used to authorize any succeeding requests>
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
    /* @var $password string */
    $password = filter_input(INPUT_POST, 'password');  
    
    $conn = Connect::connectToTaskadorDB();
    
    // Get the hashed password from the account. This will be used to verify if
    // the input password is correct.
    $correctPassword = Account::getPassword($conn, $username);
    
    if ($correctPassword != false) {
        // If the input password is verified to be correct, then try to send the
        // auth token. The getToken function checks if the token is expired and
        // will generate a new one if it is.
        if (Password::verifyPassword($password, $correctPassword)) {
            try {
                $token = Account::getToken($conn, $username);
            } catch (PDOException $ex) {
                Response::errorResponse(500, 'Exception while getting token: '
                        . $ex->getMessage());
            }
            
            if ($token == false) {
                Response::errorResponse(422, 'Username does not exist');
            }
            
            $response = ['token' => $token];
            Response::send($response);
        } else {
            Response::errorResponse(422, 'Incorrect password.');
        }
    } else {
        Response::errorResponse(422, 'Username does not exist');
    }
} else {
    Response::errorResponse(405, 'Method is not POST');
}