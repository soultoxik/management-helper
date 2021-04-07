<?php

require_once __DIR__ . '/../vendor/autoload.php';
$openapi = \OpenApi\scan(__DIR__ . '/../app/Controllers');
header('Content-Type: application/json');
echo $openapi->toJson();
