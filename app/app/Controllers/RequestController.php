<?php

namespace App\Controllers;

use App\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class RequestController
{
    public function getStatus(ServerRequestInterface $request, array $args)
    {
        return JsonResponse::respond(['status' => 'success']);
    }
}