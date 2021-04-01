<?php


namespace App\Queue\Jobs;


abstract class Job
{
    protected bool $completed = false;


    public function do()
    {
        if ($this->work()) {
            $this->setCompleted();
        }
    }

    protected function setCompleted(): void
    {
//        AppLogger::addInfo('RabbitMQ:Consumer job - completed');
        $this->completed = true;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    abstract protected function work(): bool;
}