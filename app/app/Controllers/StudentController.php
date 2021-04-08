<?php

namespace App\Controllers;

use App\Helpers\JSONHelper;
use App\Models\User;
use App\Queue\Jobs\Worker;
use App\Queue\RabbitMQProducer;
use App\Repository\RequestRepository;
use App\Repository\StudentRepository;
use App\Response\JsonResponse;
use App\Storage\RedisDAO;
use App\Validators\RequestValidator;
use Psr\Http\Message\ServerRequestInterface;

class StudentController
{

    const RULES_VALIDATE = [
        'email' => 'required|email',
        'first_name' => 'required',
        'last_name' => 'required',
        'phone' => 'required',
        'enabled' => 'required|boolean',
        'skills' => 'required|array',
    ];

    /**
     * @OA\Post (
     *     path="/api/v1/students",
     *     tags={"Student API"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Group name.",
     *                     property="name",
     *                     type="string",
     *                     example="Bob"
     *                 ),
     *                 required={"name"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="OK", @OA\JsonContent()),
     *     @OA\Response(response="default", description="Error", @OA\JsonContent()),
     * )
     */
    public function create(ServerRequestInterface $request, array $args)
    {
        try {
            $body = $request->getBody()->getContents();
            $this->validateCreate($body);

            $body = json_decode($body, true);
            $user = $this->prepareCreateUser($body);

            $skills = $body['skills'];
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

    /**
     * @OA\Get(
     *     path="/api/v1/students/{id}",
     *     tags={"Student API"},
     *
     *     @OA\Parameter(name="id", in="path", description="The student identifier.", example=1, required=true),
     *
     *     @OA\Response(response="200", description="OK", @OA\JsonContent()),
     *     @OA\Response(response="default", description="Error", @OA\JsonContent()),
     * )
     */
    public function search(ServerRequestInterface $request, array $args)
    {
        $this->validateArgument($args);

        $repo = new StudentRepository(new RedisDAO());
        $student = $repo->getStudentByID($args['student_id']);
        $user = $student->user->toArray();
        unset($user['skills']);
        $data = [
            'user' => $user,
            'skills' => $student->skills->pluck('id')->toArray(),
        ];
        return JsonResponse::respond($data);
    }

    /**
     * @OA\Patch  (
     *     path="/api/v1/students/{id}",
     *     tags={"Student API"},
     *
     *     @OA\Parameter(name="id", in="path", description="The student identifier.", example=1, required=true),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="student name.",
     *                     property="name",
     *                     type="string",
     *                     example="Nicolas"
     *                 ),
     *                 required={"name"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="OK", @OA\JsonContent()),
     *     @OA\Response(response="default", description="Error", @OA\JsonContent()),
     * )
     */
    public function update(ServerRequestInterface $request, array $args)
    {
        try {
            $body = $request->getBody()->getContents();
            $this->validateUpdate($body, $args);

            $body = json_decode($body, true);
            $user = $this->prepareUpdateUser($args['student_id'], $body);

            $skills = $body['skills'];

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

    /**
     * @OA\Delete (
     *     path="/api/v1/students/{id}",
     *     tags={"Student API"},
     *
     *     @OA\Parameter(name="id", in="path", description="The student identifier.", example=1, required=true),
     *
     *     @OA\Response(response="200", description="OK", @OA\JsonContent()),
     *     @OA\Response(response="default", description="Error", @OA\JsonContent()),
     * )
     */
    public function delete(ServerRequestInterface $request, array $args)
    {
        try {
            $this->validateArgument($args);
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

    private function validateCreate(string $body): void
    {
        $rules = self::RULES_VALIDATE;
        unset($rules['enabled']);
        $this->validateBody($body, $rules);
    }

    private function validateUpdate(string $body, array $args): void
    {
        $this->validateArgument($args);
        $this->validateBody($body, self::RULES_VALIDATE);
    }

    private function validateArgument(array $args)
    {
        $validator = new RequestValidator($args);
        $validator->validate(['student_id' => 'required|numeric']);
    }

    private function validateBody(string $body, array $rules)
    {
        if (!JSONHelper::isJSON($body)) {
            throw new \Exception('Received string is not in JSON-format.');
        }
        $data = json_decode($body, true);
        $validator = new RequestValidator($data);
        $validator->validate($rules);
    }

    private function prepareCreateUser(array $body): User
    {
        $user = $this->prepareUser($body);
        $user->enabled = true;
        $user->teacher = false;
        return $user;
    }

    private function prepareUpdateUser(int $studentID, array $body): User
    {
        $user = $this->prepareUser($body);
        $user->id = $studentID;
        $user->teacher = false;
        return $user;
    }

    private function prepareUser(array $body): User
    {
        $properties = array_keys(self::RULES_VALIDATE);

        $user = new User;
        foreach ($properties as $item) {
            if ($item == 'skills') {
                continue;
            }
            $user->{$item} = $body[$item];
        }

        return $user;
    }
}