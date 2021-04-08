<?php
require_once '../bootstrap/bootstrap.php';

use App\Queue\RabbitMQConsumer;

$command = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'create_group';
try {
    $producer = new RabbitMQConsumer();
    $producer->consume($command);
} catch (\Exception $e) {
    \App\Logger\AppLogger::addEmergency($e->getMessage());
}
