<?php
require_once '../../autoload.php';

$message = 'Server Error!!!';

Response::errorResponse(500, $message);
