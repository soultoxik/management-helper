<?php

namespace App\Controllers;

use App\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class StudentController
{
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
    public function create()
    {
        return JsonResponse::respond([],201);
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
    public function search()
    {
        $data = ['asd' => 123];
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
        $data = ['asd' => 123, 'args' => $args];
        return JsonResponse::respond($data);
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
        $data = ['asd' => 123, 'args' => $args];
        return JsonResponse::respond($data);
    }
}