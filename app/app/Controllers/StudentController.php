<?php

namespace App\Controllers;

use App\Queue\Jobs\Worker;
use App\Queue\RabbitMQProducer;
use App\Repository\RequestRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use App\Response\JsonResponse;
use App\Validators\RequestValidator;
use League\Route\Http\Exception\BadRequestException;
use Psr\Http\Message\ServerRequestInterface;

class StudentController
{
    public function create()
    {
        return JsonResponse::respond([],201);
    }

    public function search()
    {
        $data = ['asd' => 123];
        return JsonResponse::respond($data);
    }

    public function update(ServerRequestInterface $request, array $args)
    {
        $data = ['asd' => 123, 'args' => $args];
        return JsonResponse::respond($data);
    }

    public function delete(ServerRequestInterface $request, array $args)
    {
        $data = ['asd' => 123, 'args' => $args];
        return JsonResponse::respond($data);
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return \Laminas\Diactoros\Response|\Psr\Http\Message\ResponseInterface
     * @throws \League\Route\Http\Exception\BadRequestException
     * @throws \League\Route\Http\Exception\NotFoundException
     */
    public function findGroup(ServerRequestInterface $request, array $args)
    {
        (new RequestValidator($args))->validate(['user_id' => 'required|numeric']);

        $user = (new UserRepository())->findById($args['user_id']);

        if ($user->isTeacher()) {
            throw new BadRequestException('user is a student');
        }

        $queueRequest = RequestRepository::createRequest();

        $data = [
            'request_id' => $queueRequest->id,
            'id' => $user->id
        ];

        $producer = new RabbitMQProducer();
        $producer->publish(Worker::COMMAND_FIND_GROUP_NEW_USER, $data);

        return JsonResponse::respond(['result' => $queueRequest->id]);
    }
}