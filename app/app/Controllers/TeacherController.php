<?php

namespace App\Controllers;

use App\Helpers\JSONHelper;
use App\Models\DTOs\TeacherConditionDTO;
use App\Models\TeacherCondition;
use App\Models\User;
use App\Repository\TeacherRepository;
use App\Repository\UserRepository;
use App\Response\JsonResponse;
use App\Storage\RedisDAO;
use App\Validators\RequestValidator;
use Psr\Http\Message\ServerRequestInterface;

class TeacherController
{
    public const USER_RULES_VALIDATE
        = [
            'email'      => 'required|email',
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'required',
            'enabled'    => 'required|boolean',
            'skills'     => 'required|array',
        ];

    public const TEACHER_RULES_VALIDATE
        = [
            'max_groups_num' => 'required|numeric',
            'min_group_size' => 'required|numeric',
            'max_group_size' => 'required|numeric',
        ];

    public function create(ServerRequestInterface $request, array $args)
    {
        try {
            $body = $request->getBody()->getContents();
            $this->validateCreate($body);

            $body = json_decode($body, true);
            $user = $this->prepareCreateUser($body);

            $skills = $body['skills'];
            $teacherCondition = $this->prepareCreateTeacherCondition($body);
            $repo = new TeacherRepository(new RedisDAO());
            $teacher = $repo->create($user, $skills, $teacherCondition);
            if (empty($teacher)) {
                throw new \Exception('Teacher was not create');
            }

            $data = ['teacher_id' => $teacher->user->id];
            $status = 201;
        } catch (\Exception $e) {
            $data = ['message' => $e->getMessage()];
            $status = 422;
        }
        return JsonResponse::respond($data, $status);
    }

    public function search(ServerRequestInterface $request, array $args)
    {
        $this->validateArgument($args);

        $repo = new TeacherRepository(new RedisDAO());
        $teacher = $repo->getTeacherByID($args['teacher_id']);
        $user = $teacher->user->toArray();
        unset($user['skills']);
        $data = [
            'user' => $user,
            'skills' => $teacher->skills->pluck('id')->toArray(),
            'teacher_condition' => $user['teacher_conditions'],
        ];
        return JsonResponse::respond($data);
    }

    public function update(ServerRequestInterface $request, array $args)
    {
        try {
            $body = $request->getBody()->getContents();
            $this->validateUpdate($body, $args);

            $body = json_decode($body, true);
            $user = $this->prepareUpdateUser($args['teacher_id'], $body);

            $skills = $body['skills'];

            $teacherCondition = $this->prepareUpdateTeacherCondition(
                $user->id,
                $body
            );

            $repo = new TeacherRepository(new RedisDAO());
            $result = $repo->update($user, $skills, $teacherCondition);
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
            $this->validateArgument($args);
            $repo = new TeacherRepository(new RedisDAO());
            $result = $repo->delete($args['teacher_id']);
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

        $user = (new UserRepository())->findById($args['user_id']);
        $student = new TeacherRepository($user);
        $student->findSuitableGroup();
        $student->addToGroup();

        return JsonResponse::respond(['result' => $student->getGroup()]);
    }

    private function validateCreate(string $body): void
    {
        $rules = array_merge(self::USER_RULES_VALIDATE, self::TEACHER_RULES_VALIDATE);
        unset($rules['enabled'], $rules['user_id']);
        $this->validateBody($body, $rules);
    }

    private function validateUpdate(string $body, array $args): void
    {
        $this->validateArgument($args);
        $this->validateBody(
            $body,
            array_merge(self::USER_RULES_VALIDATE, self::TEACHER_RULES_VALIDATE)
        );
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

    private function validateArgument(array $args)
    {
        $validator = new RequestValidator($args);
        $validator->validate(['teacher_id' => 'required|numeric']);
    }

    private function prepareCreateUser(array $body): User
    {
        $user = $this->prepareUser($body);
        $user->enabled = true;
        $user->teacher = true;
        return $user;
    }

    private function prepareUpdateUser(int $studentID, array $body): User
    {
        $user = $this->prepareUser($body);
        $user->id = $studentID;
        $user->teacher = true;
        return $user;
    }

    private function prepareUser(array $body): User
    {
        $properties = array_keys(self::USER_RULES_VALIDATE);

        $user = new User;
        foreach ($properties as $item) {
            if ($item == 'skills') {
                continue;
            }
            $user->{$item} = $body[$item];
        }

        return $user;
    }

    private function prepareCreateTeacherCondition(array $body): TeacherConditionDTO
    {
        return new TeacherConditionDTO(
            null,
            $body['max_groups_num'],
            $body['min_group_size'],
            $body['max_group_size'],
        );
    }

    private function prepareUpdateTeacherCondition(int $teacherID, array $body): TeacherCondition
    {
        $properties = array_keys(self::TEACHER_RULES_VALIDATE);

        $teacherCondition = new TeacherCondition();
        $teacherCondition->user_id = $teacherID;
        foreach ($properties as $item) {
            $teacherCondition->{$item} = $body[$item];
        }

        return $teacherCondition;
    }
}
