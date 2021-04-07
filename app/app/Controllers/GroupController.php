<?php

namespace App\Controllers;

use App\Models\DTOs\GroupDTO;
use App\Models\Request;
use App\Repository\GroupRepository;
use App\Response\JsonResponse;
use App\Validators\RequestValidator;
use Psr\Http\Message\ServerRequestInterface;
use App\Models\Group;

class GroupController
{
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

    public function findTeacher(ServerRequestInterface $request, array $args)
    {
        (new RequestValidator($args))->validate(['group_id' => 'required|numeric']);

        $group = new GroupRepository();
        $group->findById($args['group_id']);
        $group->findSuitableTeacher();
        $group->addToGroup();
        return JsonResponse::respond(['result' => $group->getTeacher()]);
    }

    public function changeTeacher(ServerRequestInterface $request, array $args)
    {
        $data = json_decode($request->getBody()->getContents(), true);
        (new RequestValidator($data))->validate(['teacher_id' => 'required|numeric']);

        $group = new GroupRepository();
        $group->findById($args['group_id']);
        $group->changeTo($data['teacher_id']);
        return JsonResponse::respond(['result' => $data]);
    }
}