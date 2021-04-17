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

    /**
     * @OA\Post (
     *     path="/api/v1/students",
     *     tags={"Student API"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", example="junior@email.com"),
     *                 @OA\Property(property="first_name", example="Bob"),
     *                 @OA\Property(property="last_name", example="Jordan"),
     *                 @OA\Property(property="phone", example="+7 495 1111111"),
     *                 @OA\Property(property="skills", example="[2, 3, 4]")
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

    /**
     * @OA\Get(
     *     path="/api/v1/students/{id}",
     *     tags={"Student API"},
     *     @OA\Parameter(name="id", in="path", description="The identifier.", example=1, required=true),
     *     @OA\Response(response="200", description="OK"),
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/students/{id}/groups",
     *     tags={"Student API"},
     *     @OA\Parameter(name="id", in="path", description="The identifier.", example=1, required=true),
     *     @OA\Response(response="200", description="OK"),
     * )
     */
    public function searchGroups(ServerRequestInterface $request, array $args)
    {
        $this->validator->validateArgument($args);
        $groupsIDs = $this->studentRepo->getGroupIDsByStudentID($args['student_id']);
        $data = [
            'student_id' => $args['student_id'],
            'group_ids' => $groupsIDs,
        ];
        return JsonResponse::respond($data);
    }

    /**
     * @OA\Patch  (
     *     path="/api/v1/students/{id}",
     *     tags={"Student API"},
     *     @OA\Parameter(name="id", in="path", description="The identifier.", example=1, required=true),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", example="junior@email.com"),
     *                 @OA\Property(property="first_name", example="Bob"),
     *                 @OA\Property(property="last_name", example="Jordan"),
     *                 @OA\Property(property="phone", example="+7 495 3333333"),
     *                 @OA\Property(property="enabled", example=false),
     *                 @OA\Property(property="skills", example="[3, 4, 5]")
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

    /**
     * @OA\Delete (
     *     path="/api/v1/students/{id}",
     *     tags={"Student API"},
     *     @OA\Parameter(name="id", in="path", description="The identifier.", example=1, required=true),
     *     @OA\Response(response="201", description="OK"),
     * )
     */
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

    /**
     * @OA\Post (
     *     path="/api/v1/student/{id}/find-group",
     *     tags={"Student API"},
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
            $args['student_id'],
            Worker::COMMAND_STUDENT_FIND_GROUP
        );

        return JsonResponse::respond($data);
    }
}
