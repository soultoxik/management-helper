<?php


namespace App\Queue;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQ
{
    const EXCHANGE = 'custom_exchange';
    const QUEUE_NAME = 'custom_queue_name';
    const ROUTING_KEY = 'custom_routing_key';

    protected AMQPStreamConnection $connection;
    protected AMQPChannel $channel;

    public function __construct()
    {
        $this->setChannelConnection();
        $this->queueDeclare();
    }

    public function __destruct()
    {
        $this->closeChannelConnection();
    }

    private function setChannelConnection(): void
    {
        $this->connection = new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'],
            $_ENV['RABBITMQ_PORT'],
            $_ENV['RABBITMQ_USER'],
            $_ENV['RABBITMQ_PASSWORD']
        );
        $this->channel = $this->connection->channel();
    }

    private function queueDeclare(): void
    {
        $this->channel->queue_declare(
            self::QUEUE_NAME,
            false,
            true,
            false,
            false
        );
    }

    private function closeChannelConnection(): void
    {
        $this->channel->close();
        $this->connection->close();
    }


}