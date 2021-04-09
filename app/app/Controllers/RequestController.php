<?php

namespace App\Controllers;

use App\Repository\RequestRepository;
use App\Response\JsonResponse;
use App\Validators\RequestControllerValidator;
use Psr\Http\Message\ServerRequestInterface;

class RequestController extends Controller
{

    protected RequestRepository $requestRepo;

    public function __construct()
    {
        parent::__construct();
        $this->validator = new RequestControllerValidator();
        $this->requestRepo = new RequestRepository($this->redis);
    }


    /**
     * @OA\Get(
     *     path="/api/v1/requests/{id}",
     *     tags={"QueueRequest API"},
     *     @OA\Parameter(name="id", in="path", description="The identifier.", example=1, required=true),
     *     @OA\Response(response="201", description="OK"),
     * )
     */
    public function getStatus(ServerRequestInterface $request, array $args)
    {
        try {
            $this->validator->validateArgument($args);
            $status = $this->requestRepo->getStatus($args['request_id']);
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
}
