<?php


namespace App\Queue;

interface QueueConsumerInterface
{
    public function consume(): void;
}