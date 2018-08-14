<?php
require_once __DIR__ . '/../../autoload.php';

$message = 'Server Error!!!';

echo 'hello world'; //This is expected to be deleted by ob_clean

Response::errorResponse(500, $message);
