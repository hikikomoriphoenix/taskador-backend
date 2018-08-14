<?php
require_once __DIR__ . '/../../autoload.php';

$data = ['number' => 4];

Response::send($data);