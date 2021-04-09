<?php


namespace App\Controllers;

use App\Queue\RabbitMQProducer;
use App\Repository\RequestRepository;
use App\Storage\RedisDAO;
use App\Validators\ControllerValidatorInterface;

abstract class Controller
{

    protected RedisDAO $redis;
    protected ControllerValidatorInterface $validator;

    public function __construct()
    {
        $this->redis = new RedisDAO();
    }

    protected function asyncRequest(int $id, string $command): array
    {
        $queueRequest = RequestRepository::createRequest();
        $data = [
            'request_id' => $queueRequest->id,
            'id' => $id
        ];

        $producer = new RabbitMQProducer();
        $producer->publish($command, $data);

        return $data;
    }
}
