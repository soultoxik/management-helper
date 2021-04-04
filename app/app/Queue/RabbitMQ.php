<?php


namespace App\Queue;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class RabbitMQ
{
    protected const NAME = 'school.manager.';
    protected const EXCHANGE = self::NAME . 'exchange';

    protected AMQPStreamConnection $connection;
    protected AMQPChannel $channel;

    public function __construct()
    {
        $this->setChannelConnection();
        $this->exchangeDeclare();
    }

    private function exchangeDeclare(): void
    {
        $this->channel->exchange_declare(
            self::EXCHANGE,
            AMQPExchangeType::DIRECT,
            false,
            true,
            false
        );
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

    private function closeChannelConnection(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
