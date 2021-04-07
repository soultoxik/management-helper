<?php

namespace App\Controllers;

use App\Response\JsonResponse;
use League\Route\Http\Exception\BadRequestException;
use Psr\Http\Message\ServerRequestInterface;

class IndexController
{
    public function index(ServerRequestInterface $request)
    {
        try {
            return JsonResponse::respond('ok', 201);
        } catch (\Exception $exception) {
            return JsonResponse::respond(['message' => $exception->getMessage()], 422);
        }
    }

    public function test()
    {
        $data = ['asd' => 123];
        return JsonResponse::respond($data, 402);
    }
}