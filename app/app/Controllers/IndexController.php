<?php

namespace App\Controllers;

use App\Models\DTOs\GroupDTO;
use App\Models\DTOs\TeacherConditionDTO;
use App\Models\Group;
use App\Models\Skill;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherCondition;
use App\Models\User;
use App\Queue\RabbitMQProducer;
use App\Repository\GroupRepository;
use App\Repository\TeacherRepository;
use App\Response\JsonResponse;
use App\Storage\RedisDAO;
use Psr\Http\Message\ServerRequestInterface;

class IndexController
{
    public function index(ServerRequestInterface $request)
    {
        try {

            $producer = new RabbitMQProducer();
            $producer->publish('create_group', ['request_id' => 1, 'id'=> 123]);
            return JsonResponse::respond('ok', 201);
        } catch (\Exception $exception) {
            return JsonResponse::respond(['message' => $exception->getMessage()], 422);
        }
    }

    public function test()
    {
        $data = ['asd' => 123];
        return JsonResponse::respond($data, 201);
    }
}
