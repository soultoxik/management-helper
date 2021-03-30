<?php


namespace App\Services;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueService
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function receive(): void
    {
        $this->setChannelConnection();

        $this->channel->queue_declare(
            $_ENV['RABBITMQ_QUEUE'],
            false,
            false,
            false,
            false
        );

        echo " [*] Waiting for input data. To exit press CTRL+C\n";

        $callback = function ($inputData) {
            // do something with $inputData
        };

        $this->channel->basic_consume(
            $_ENV['RABBITMQ_QUEUE'],
            '',
            false,
            true,
            false,
            false,
            $callback
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function send($inputData): void
    {
        $this->setChannelConnection();

        $this->channel->queue_declare(
            $_ENV['RABBITMQ_QUEUE'],
            false,
            false,
            false,
            false
        );

        $msg = new AMQPMessage($inputData);
        $this->channel->basic_publish(
            $msg,
            '',
            $_ENV['RABBITMQ_QUEUE']
        );

//        echo " [x] Sent $inputData\n";

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