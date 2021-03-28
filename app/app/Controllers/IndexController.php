<?php
namespace App\Controllers;

use App\Response\JsonResponse;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ServerRequestInterface;

class IndexController
{
    public function index(ServerRequestInterface $request): array
    {
        $test = $request->getQueryParams();

        return ['message'=>'hello world','queryParams' => $test];
    }

    public function test()
    {
        $data = ['asd' => 123];
        return JsonResponse::respond($data,201);
    }
}