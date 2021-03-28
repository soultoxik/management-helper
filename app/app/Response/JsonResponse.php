<?php

namespace App\Response;

use Laminas\Diactoros\Response;

class JsonResponse
{
    public static function respond($data, int $status = 200)
    {
        $response = new Response();
        $response->getBody()->write(json_encode($data));
        return $response->withStatus($status);
    }
}