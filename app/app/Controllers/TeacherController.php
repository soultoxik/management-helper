<?php

namespace App\Controllers;

use App\Models\DTOs\TeacherConditionDTO;
use App\Models\TeacherCondition;
use App\Queue\Jobs\Worker;
use App\Repository\TeacherRepository;
use App\Response\JsonResponse;
use App\Validators\TeacherControllerValidator;
use Psr\Http\Message\ServerRequestInterface;

class TeacherController extends Controller
{

    use UserControllerTrait;

    protected TeacherRepository $teacherRepo;

    public function __construct()
    {
        parent::__construct();
        $this->validator = new TeacherControllerValidator();
        $this->teacherRepo = new TeacherRepository($this->redis);
    }

    /**
     * @OA\Post (
     *     path="/api/v1/teachers",
     *     tags={"Teacher API"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", example="junior@email.com"),
     *                 @OA\Property(property="first_name", example="Bob"),
     *                 @OA\Property(property="last_name", example="Jordan"),
     *                 @OA\Property(property="phone", example="+7 495 1111111"),
     *                 @OA\Property(property="skills", example="[2, 3, 4]"),
     *                 @OA\Property(property="max_groups_num", example=5),
     *                 @OA\Property(property="min_group_size", example=5),
     *                 @OA\Property(property="max_group_size", example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="OK"),
     * )
     */
    public function create(ServerRequestInterface $request, array $args)
    {
        try {
            $body = $request->getBody()->getContents();
            $this->validator->validateCreate($body);

            $body = json_decode($body, true);
            $user = $this->prepareCreateUser($body, true);

            $skills = $body['skills'];
            $teacherCondition = new TeacherConditionDTO(
                null,
                $body['max_groups_num'],
                $body['min_group_size'],
                $body['max_group_size'],
            );

            $teacher = $this->teacherRepo->create($user, $skills, $teacherCondition);
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

    /**
     * @OA\Get (
     *     path="/api/v1/teachers/{id}",
     *     tags={"Teacher API"},
     *     @OA\Parameter(name="id", in="path", description="The identifier.", example=1, required=true),
     *     @OA\Response(response="200", description="OK"),
     * )
     */
    public function search(ServerRequestInterface $request, array $args)
    {
        $this->validator->validateArgument($args);

        $teacher = $this->teacherRepo->getTeacherByID($args['teacher_id']);
        $user = $teacher->user->toArray();
        $teacher_conditions = $user['teacher_conditions'];
        unset($user['skills']);
        unset($user['teacher_conditions']);
        $data = [
            'user' => $user,
            'skills' => $teacher->skills->pluck('id')->toArray(),
            'teacher_condition' => $teacher_conditions,
        ];
        return JsonResponse::respond($data);
    }

    /**
     * @OA\Patch (
     *     path="/api/v1/teachers/{id}",
     *     tags={"Teacher API"},
     *     @OA\Parameter(name="id", in="path", description="The identifier.", example=1, required=true),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", example="junior@email.com"),
     *                 @OA\Property(property="first_name", example="Bob"),
     *                 @OA\Property(property="last_name", example="Jordan"),
     *                 @OA\Property(property="phone", example="+7 495 1111111"),
     *                 @OA\Property(property="enabled", example=false),
     *                 @OA\Property(property="skills", example="[3, 4, 5]"),
     *                 @OA\Property(property="max_groups_num", example=1),
     *                 @OA\Property(property="min_group_size", example=25),
     *                 @OA\Property(property="max_group_size", example=35)
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="OK"),
     * )
     */
    public function update(ServerRequestInterface $request, array $args)
    {
        try {
            $body = $request->getBody()->getContents();
            $this->validator->validateUpdate($body, $args);

            $body = json_decode($body, true);
            $user = $this->prepareUpdateUser($args['teacher_id'], $body, true);

            $skills = $body['skills'];

            $teacherCondition = new TeacherCondition();
            $teacherCondition->user_id = $args['teacher_id'];
            $teacherCondition->max_groups_num = $body['max_groups_num'];
            $teacherCondition->min_group_size = $body['min_group_size'];
            $teacherCondition->max_group_size = $body['max_group_size'];

            $result = $this->teacherRepo->update($user, $skills, $teacherCondition);
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
     *     path="/api/v1/teachers/{id}",
     *     tags={"Teacher API"},
     *     @OA\Parameter(name="id", in="path", description="The identifier.", example=1, required=true),
     *     @OA\Response(response="201", description="OK"),
     * )
     */
    public function delete(ServerRequestInterface $request, array $args)
    {
        try {
            $this->validator->validateArgument($args);
            $result = $this->teacherRepo->delete($args['teacher_id']);
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
     * @OA\Post (
     *     path="/api/v1/teacher/{id}/find-group",
     *     tags={"Teacher API"},
     *     @OA\Parameter(name="id", in="path", description="The identifier.", example=1, required=true),
     *     @OA\Response(response="201", description="OK"),
     * )
     *
     * @param ServerRequestInterface $request
     * @param array $args
     * @return \Laminas\Diactoros\Response|\Psr\Http\Message\ResponseInterface
     * @throws \League\Route\Http\Exception\BadRequestException
     * @throws \League\Route\Http\Exception\NotFoundException
     */
    public function findGroup(ServerRequestInterface $request, array $args)
    {
        $this->validator->validateArgument($args);

        $data = $this->asyncRequest(
            $args['teacher_id'],
            Worker::COMMAND_TEACHER_FIND_GROUP
        );

        return JsonResponse::respond($data);
    }

}
