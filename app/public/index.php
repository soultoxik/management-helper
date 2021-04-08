<?php

use App\App;

require_once '../bootstrap/bootstrap.php';

try {
    $app = new App();
    $app->run();
}catch (\Exception $e) {
    \App\Logger\AppLogger::addEmergency($e->getMessage());
}