<?php


namespace App\Queue;

use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQProducer extends RabbitMQ implements QueueProducerInterface
{

    /**
     * {
     *      "command": "create_group",
     *      "request_id": 1,
     *      "data": {
     *          "group_id": 1
     *          }
     * }
     * {
     *      "command": "find_teacher",
     *      "request_id": 1,
     *      "data": {
     *          "group_id": 1
     *          }
     * }
     * {
     *      "command": "find_group_new_user",
     *      "request_id": 1,
     *      "data": {
     *          "student_id": 1
     *          }
     * }
     * {
     *      "command": "replace_teacher",
     *      "request_id": 1,
     *      "data": {
     *          "group_id": 1
     *          }
     * }
     *
     */

    public function publish(string $data): void
    {
        $this->channel->exchange_declare(
            self::EXCHANGE,
            AMQPExchangeType::DIRECT,
            false,
            true,
            false
        );

        $this->channel->queue_bind(
            self::QUEUE_NAME,
            self::EXCHANGE,
            self::ROUTING_KEY
        );

        $msg = new AMQPMessage(
            $data,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish(
            $msg,
            self::EXCHANGE,
            self::ROUTING_KEY
        );
    }

}
