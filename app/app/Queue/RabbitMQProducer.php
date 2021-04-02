<?php


namespace App\Queue;

use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQProducer extends RabbitMQ implements QueueProducerInterface
{

    public function publish(string $command, array $data): void
    {
        $msg = new AMQPMessage(
            json_encode($data),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish(
            $msg,
            self::EXCHANGE,
            $command
        );
    }
}
