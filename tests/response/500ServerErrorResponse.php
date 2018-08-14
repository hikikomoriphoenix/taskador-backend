<?php
require_once __DIR__ . '/../../autoload.php';

$message = 'Server Error!!!';

Response::errorResponse(500, $message);
