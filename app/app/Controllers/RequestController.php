<?php

namespace App\Controllers;

use App\Repository\RequestRepository;
use App\Response\JsonResponse;
use App\Storage\RedisDAO;
use App\Validators\RequestValidator;
use Psr\Http\Message\ServerRequestInterface;

class RequestController
{
    public function getStatus(ServerRequestInterface $request, array $args)
    {
        try {
            $this->validateArgument($args);
            $repo = new RequestRepository(new RedisDAO());
            $status = $repo->getStatus($args['request_id']);
            $data = ['status' => $status];
            $status = 201;
        } catch (\Exception $e) {
            $data = [
                'message' => $e->getMessage(),
                'status' => false
            ];
            $status = 422;
        }
        return JsonResponse::respond($data, $status);
    }

    private function validateArgument(array $args)
    {
        $validator = new RequestValidator($args);
        $validator->validate(['request_id' => 'required|numeric']);
    }
}