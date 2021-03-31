<?php


namespace App\Queue;

interface QueueProducerInterface
{
    public function publish(string $data): void;
}