<?php

namespace App\Controllers;

use App\Queue\Jobs\Worker;
use App\Repository\StudentRepository;
use App\Response\JsonResponse;
use App\Validators\StudentControllerValidator;
use Psr\Http\Message\ServerRequestInterface;

class StudentController extends Controller
{

    use UserControllerTrait;

    protected StudentRepository $studentRepo;

    public function __construct()
    {
        parent::__construct();
        $this->validator = new StudentControllerValidator();
        $this->studentRepo = new StudentRepository($this->redis);
    }

    public function create(ServerRequestInterface $request, array $args)
    {
        try {
            $body = $request->getBody()->getContents();
            $this->validator->validateCreate($body);

            $body = json_decode($body, true);
            $user = $this->prepareCreateUser($body, false);

            $skills = $body['skills'];
            $student = $this->studentRepo->create($user, $skills);
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

    public function search(ServerRequestInterface $request, array $args)
    {
        $this->validator->validateArgument($args);

        $student = $this->studentRepo->getStudentByID($args['student_id']);
        $user = $student->user->toArray();
        unset($user['skills']);
        $data = [
            'user' => $user,
            'skills' => $student->skills->pluck('id')->toArray(),
        ];
        return JsonResponse::respond($data);
    }

    public function update(ServerRequestInterface $request, array $args)
    {
        try {
            $body = $request->getBody()->getContents();
            $this->validator->validateUpdate($body, $args);

            $body = json_decode($body, true);
            $user = $this->prepareUpdateUser($args['student_id'], $body, false);

            $skills = $body['skills'];

            $result = $this->studentRepo->update($user, $skills);
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
            $this->validator->validateArgument($args);
            $result = $this->studentRepo->delete($args['student_id']);
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

    public function findGroup(ServerRequestInterface $request, array $args)
    {
        $this->validator->validateArgument($args);

        $data = $this->asyncRequest(
            $args['student_id'],
            Worker::COMMAND_FIND_GROUP_NEW_USER
        );

        return JsonResponse::respond($data);
    }
}
