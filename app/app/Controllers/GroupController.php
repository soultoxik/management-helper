<?php

namespace App\Controllers;

use App\Models\DTOs\GroupDTO;
use App\Models\Request;
use App\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use App\Models\Group;

class GroupController
{
    /**
     * @OA\Post (
     *     path="/api/v1/groups",
     *     tags={"Group API"},
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
     *                     example="ggggroup 65"
     *                 ),
     *                 @OA\Property(
     *                     description="min_students_num.",
     *                     property="min_students_num",
     *                     type="integer",
     *                     example="3"
     *                 ),
     *                 @OA\Property(
     *                     description="max_students_num.",
     *                     property="max_students_num",
     *                     type="integer",
     *                     example="20"
     *                 ),
     *                 @OA\Property(
     *                     description="min_skills_num.",
     *                     property="min_skills_num",
     *                     type="integer",
     *                     example="5"
     *                 ),
     *                 @OA\Property(
     *                     description="max_skills_num.",
     *                     property="max_skills_num",
     *                     type="integer",
     *                     example="6"
     *                 ),
     *                 @OA\Property(
     *                     description="max_useless_skill_students.",
     *                     property="max_useless_skill_students",
     *                     type="integer",
     *                     example="1"
     *                 ),
     *                 @OA\Property(
     *                     description="Array of IDs of skills.",
     *                     property="skills",
     *                      type="array",
     *                     example="[1, 2]"
     *                 ),
     *                 required={"name, min_students_num, max_students_num"}
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
        $data = $request->getBody()->getContents();
        if ($data = json_decode($data, true)) {
            // нужна валидация
            $skills = $data['skills'];
            $data['enabled'] = false;
            unset($data['skills']);
            $groupDTO = new GroupDTO(
                $data['name'],
                null,
                $data['min_students_num'],
                $data['max_students_num'],
                $data['min_skills_num'],
                $data['max_skills_num'],
                $data['max_useless_skill_students'],
                $data['enabled']
            );
            $group = Group::insert($groupDTO, $skills);
            // нужна проверка на существования группы

            $req = Request::create(['status' => Request::OPEN]);
            // queueproducer->publish()
            $data = ['group_id' => $group->id, 'request_id' => $req->id];
        }

        return JsonResponse::respond($data,201);
    }

    /**
    * @OA\Get(
    *     path="/api/v1/groups/{id}",
    *     tags={"Group API"},
    *
    *     @OA\Parameter(name="id", in="path", description="The group identifier.", example=1, required=true),
    *
    *     @OA\Response(response="200", description="OK", @OA\JsonContent()),
    *     @OA\Response(response="default", description="Error", @OA\JsonContent()),
    * )
    */
    public function search(ServerRequestInterface $request, array $args)
    {
        // нужна валидация
        $groupID = $args['group_id'];
        $group = Group::find($groupID);
        $skills = $group->skills()->get();
        $data['skills'] = [];
        foreach ($skills as $item) {
            $data['skills'][] = $item->id;
        }
        $data['group'] = $group->toArray();
        return JsonResponse::respond($data);
    }

    /**
     * @OA\Patch  (
     *     path="/api/v1/groups/{id}",
     *     tags={"Group API"},
     *
     *     @OA\Parameter(name="id", in="path", description="The group identifier.", example=1, required=true),
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
     *                     example="ggggroup 65"
     *                 ),
     *                 @OA\Property(
     *                     description="min_students_num.",
     *                     property="min_students_num",
     *                     type="integer",
     *                     example="3"
     *                 ),
     *                 @OA\Property(
     *                     description="max_students_num.",
     *                     property="max_students_num",
     *                     type="integer",
     *                     example="20"
     *                 ),
     *                 @OA\Property(
     *                     description="min_skills_num.",
     *                     property="min_skills_num",
     *                     type="integer",
     *                     example="5"
     *                 ),
     *                 @OA\Property(
     *                     description="max_skills_num.",
     *                     property="max_skills_num",
     *                     type="integer",
     *                     example="6"
     *                 ),
     *                 @OA\Property(
     *                     description="max_useless_skill_students.",
     *                     property="max_useless_skill_students",
     *                     type="integer",
     *                     example="1"
     *                 ),
     *                 @OA\Property(
     *                     description="enabled.",
     *                     property="enabled",
     *                     type="boolean",
     *                     example=false
     *                 ),
     *                 @OA\Property(
     *                     description="Array of IDs of skills.",
     *                     property="skills",
     *                      type="array",
     *                     example="[1, 2, 3]"
     *                 ),
     *                 required={"name, min_students_num, max_students_num"}
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
        $data = $request->getBody()->getContents();
        if ($data = json_decode($data, true)) {
            // нужна валидация
            $skills = $data['skills'];
            unset($data['skills']);
            $group = Group::where('name', $data['name'])->first();
            $group->name = $data['name'];
            $group->min_students_num = $data['min_students_num'];
            $group->max_students_num = $data['max_students_num'];
            $group->min_skills_num = $data['min_skills_num'];
            $group->max_skills_num = $data['max_skills_num'];
            $group->max_useless_skill_students = $data['max_useless_skill_students'];
            $group->enabled = $data['enabled'];
            $result = Group::change($group, $skills);
            $req = Request::create(['status' => Request::OPEN]);
            // @TODO нужна очередь ведь изменение идет группы  и
            // поэтому таблицы groups_users может быть изменена queueproducer->publish()
            $data = ['updated' => $result, 'request_id' => $req->id];
        }
        return JsonResponse::respond($data);
    }

    /**
     * @OA\Delete (
     *     path="/api/v1/groups/{id}",
     *     tags={"Group API"},
     *
     *     @OA\Parameter(name="id", in="path", description="The group identifier.", example=1, required=true),
     *
     *     @OA\Response(response="200", description="OK", @OA\JsonContent()),
     *     @OA\Response(response="default", description="Error", @OA\JsonContent()),
     * )
     */
    public function delete(ServerRequestInterface $request, array $args)
    {
        // нужна валидация
        $groupID = $args['group_id'];
        $result = Group::remove($groupID);
        $req = Request::create(['status' => Request::OPEN]);
        $data = ['deleted' => $result, 'request_id' => $req->id];
        // @TODO нужна ли тут очередь?
        return JsonResponse::respond($data);
    }
}