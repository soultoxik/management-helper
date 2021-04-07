<?php
require_once '../bootstrap/bootstrap.php';

use App\Queue\RabbitMQConsumer;

$command = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'create_group';

$producer = new RabbitMQConsumer();
$producer->consume($command);