<?php

namespace App\Controllers;

use App\Response\JsonResponse;
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
}