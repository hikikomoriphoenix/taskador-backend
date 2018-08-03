<?php
header('Content-type: application/json');

/**
 * Sets response code to HTTP 200 OK and outputs the response body in JSON 
 * format before finally ending the script execution. There should be no output
 * before this function is invoked, otherwise, all the previous output will be
 * erased.
 * 
 * @param Array $responseData An array that should contain the entire response
 * body.
 */
function send($responseData) {
    ob_clean();
    http_response_code(200);
    $response = json_encode($responseData);
    exit($response);
}

/**
 * Sets response code to the given value and outputs given error message in a 
 * JSON formatted response body. Any previous output will be erased.
 *  
 * @param Integer $responseCode the appropriate response code for the error
 * @param String $message a string describing the error
 */
function errorResponse($responseCode, $message) {
    ob_clean();
    http_response_code($responseCode);
    $response = json_encode(array(
        'message' => $message
    ));
    exit($response);
}