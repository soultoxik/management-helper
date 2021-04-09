<?php


namespace App\Queue\Jobs;

use App\Logger\AppLogger;
use App\Storage\RedisDAO;
use App\Storage\Cache;

abstract class Job
{
    const SUCCESS = 'completed';

    protected bool $completed = false;
    protected Cache $cache;

    public function __construct()
    {
        $this->cache = new RedisDAO();
    }

    public function do()
    {
        if ($this->work()) {
            $this->setCompleted();
        }
    }

    protected function setCompleted(): void
    {
        AppLogger::addInfo('RabbitMQ:Consumer job - completed');
        $this->completed = true;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function getStatusSuccess(): string
    {
        return self::SUCCESS;
    }

    abstract protected function work(): bool;
    abstract public function getStatusFail(): string;
}
