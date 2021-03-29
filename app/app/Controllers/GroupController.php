<?php

namespace App\Controllers;

use App\Response\JsonResponse;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ServerRequestInterface;

class GroupController
{
    public function create()
    {
        $data = ['asd' => 123];
        return JsonResponse::respond($data,201);
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

    public function delete()
    {
        $data = ['asd' => 123];
        return JsonResponse::respond($data);
    }
}