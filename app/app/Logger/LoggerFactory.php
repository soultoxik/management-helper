<?php


namespace App\Logger;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Exception;

class LoggerFactory
{
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance != null) {
            return self::$instance;
        }
        $log = new Logger($_ENV['LOG_NAME_CHANNEL']);
        $log->pushHandler(self::getHandler($_ENV['LOG_TYPE']));

        self::$instance = $log;
        return self::$instance;
    }


    private function getHandler(string $type): HandlerInterface
    {
        switch ($type) {
            case 'file':
                $file = self::getFilePath();
                $handler = new StreamHandler($file);
                break;
        }

        return $handler;
    }

    private function getFilePath(): string
    {
        if (empty($_ENV['LOG_FILE_PATH'])) {
            throw new Exception('Check config.yaml, it has not "file_section"');
        }
        return $_ENV['LOG_FILE_PATH'];
    }
}
