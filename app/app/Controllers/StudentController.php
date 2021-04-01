<?php

namespace App\Controllers;

use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use App\Response\JsonResponse;
use App\Validators\RequestValidator;
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
        $student = new StudentRepository($user);

        return JsonResponse::respond($student->findSuitableGroup());
    }
}