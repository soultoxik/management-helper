<?php


namespace App\Queue;

interface QueueProducerInterface
{
    public function publish(string $command, array $data): void;
}