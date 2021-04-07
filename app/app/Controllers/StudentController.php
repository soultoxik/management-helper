<?php

namespace App\Controllers;

use App\Logger\AppLogger;
use App\Models\User;
use App\Queue\Jobs\Worker;
use App\Queue\RabbitMQProducer;
use App\Repository\GroupRepository;
use App\Repository\RequestRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use App\Response\JsonResponse;
use App\Storage\RedisDAO;
use App\Validators\RequestValidator;
use League\Route\Http\Exception\BadRequestException;
use Psr\Http\Message\ServerRequestInterface;

class StudentController
{
    public function create(ServerRequestInterface $request, array $args)
    {
        try {
            $data = $request->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception('JSON is wrong');
            }
//            // нужна валидация
//            if (empty(validate)) {
//                throw new \Exception('Параметры ошибочны');
//            }

            $skills = $data['skills'];
            $data['enabled'] = false;
            unset($data['skills']);
            $user = new User;
            $user->email = $data['email'];
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->phone = $data['phone'];
            $user->enabled = $data['enabled'];
            $user->teacher = false;
            $repo = new StudentRepository(new RedisDAO());
            $student = $repo->create($user, $skills);
            if (empty($student)) {
                throw new \Exception('Student was not create');
            }

            $data = ['student_id' => $student->user->id];
            $status = 201;

        } catch (\Exception $e) {
            $data = ['message' => $e->getMessage()];
            $status = 422;
        }
        return JsonResponse::respond($data, $status);
    }

    public function search()
    {
        $data = ['asd' => 123];
        return JsonResponse::respond($data);
    }

    public function update(ServerRequestInterface $request, array $args)
    {
        try {
            $data = $request->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception('JSON is wrong');
            }

            $skills = $data['skills'];
            $data['enabled'] = false;
            unset($data['skills']);
            $user = new User;
            $user->id = $args['student_id'];
            $user->email = $data['email'];
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->phone = $data['phone'];
            $user->enabled = $data['enabled'];
            $user->teacher = false;
            $repo = new StudentRepository(new RedisDAO());
            $result = $repo->update($user, $skills);
            $status = 201;
            if (empty($result)) {
                $status = 422;
            }
            $data = ['updated' => $result];
        } catch (\Exception $e) {
            $data = [
                'message' => $e->getMessage(),
                'updated' => false
            ];
            $status = 422;
        }
        return JsonResponse::respond($data, $status);
    }

    public function delete(ServerRequestInterface $request, array $args)
    {
        try {
            $repo = new StudentRepository(new RedisDAO());
            $result = $repo->delete($args['student_id']);
            $data = ['deleted' => $result];
            $status = 201;
        } catch (\Exception $e) {
            $data = [
                'message' => $e->getMessage(),
                'deleted' => false
            ];
            $status = 422;
        }

        return JsonResponse::respond($data, $status);
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

//        $student = new StudentRepository($this->student);
//        $student->findSuitableGroup();
//        $student->addToGroup();
//        $user = (new UserRepository())->findById($args['user_id']);
//
//        if ($user->isTeacher()) {
//            throw new BadRequestException('user is a student');
//        }

        $queueRequest = RequestRepository::createRequest();

        $data = [
            'request_id' => $queueRequest->id,
            'id' => $args['user_id']
        ];

        $producer = new RabbitMQProducer();
        $producer->publish(Worker::COMMAND_FIND_GROUP_NEW_USER, $data);

        return JsonResponse::respond(['id' => $queueRequest->id]);
    }
}
