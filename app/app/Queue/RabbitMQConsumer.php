<?php


namespace App\Queue;

use App\Exceptions\RabbitMQConsumerException;
use App\Logger\AppLogger;
use App\Queue\Jobs\Worker;

class RabbitMQConsumer extends RabbitMQ implements QueueConsumerInterface
{

    protected const ALLOWED_COMMANDS = [
        Worker::COMMAND_CREATE_GROUP,
        Worker::COMMAND_FIND_TEACHER,
        Worker::COMMAND_FIND_GROUP_NEW_USER,
        Worker::COMMAND_REPLACE_TEACHER
    ];

    public function consume(string $command): void
    {
        AppLogger::addInfo('RabbitMQ:Consumer was ran, command:' . $command);

        $this->validate($command);

        $queueName = $this->generateQueueName($command);

        $this->channel->queue_declare(
            $queueName,
            false,
            true,
            false,
            false
        );

        $this->channel->queue_bind(
            $queueName,
            self::EXCHANGE,
            $command
        );

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume(
            $queueName,
            '',
            false,
            false,
            false,
            false,
            [$this, 'processMessage']
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function processMessage($msg): void
    {
        AppLogger::addInfo('RabbitMQ:Consumer received message', [$msg->body]);
        try {
            $worker = new Worker($msg->body, $msg->getRoutingKey());
            $job = $worker->createJob();
            $job->do();
            if ($job->isCompleted()) {
                $worker->finish();
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            } else {
                // если работа не выполнена, но структура сообщения валидное, значит обратно в очередь
                $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag'], false, true);
            }
        } catch (\Exception $e) {
            AppLogger::addInfo('RabbitMQ:Consumer ' . $e->getMessage());
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
        }
    }

    /**
     * @param string $routingKey
     *
     * @throws RabbitMQConsumerException
     */
    private function validate(string $routingKey)
    {
        if (!in_array($routingKey, self::ALLOWED_COMMANDS)) {
            throw new RabbitMQConsumerException('Command: ' . $routingKey . ' not supported');
        }
    }

    private function generateQueueName(string $command): string
    {
        return self::NAME . $command;
    }
}
