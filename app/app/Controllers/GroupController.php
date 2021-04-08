<?php

namespace App\Controllers;

use App\Models\DTOs\GroupDTO;
use App\Queue\Jobs\Worker;
use App\Queue\RabbitMQProducer;
use App\Repository\GroupRepository;
use App\Repository\RequestRepository;
use App\Repository\TeacherRepository;
use App\Response\JsonResponse;
use App\Storage\RedisDAO;
use App\Validators\RequestValidator;
use Psr\Http\Message\ServerRequestInterface;
use App\Models\Group;

class GroupController
{
    public function create(ServerRequestInterface $request, array $args)
    {
        try {
            $data = $request->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception('JSON is wrong');
            }
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
                false
            );
            $repo = new GroupRepository(new RedisDAO());
            $group = $repo->create($groupDTO);
            if (empty($group)) {
                throw new \Exception('Group was not create');
            }
            $result = $repo->setSkillsByGroupID($group->id, $skills);
            if (!$result) {
                throw new \Exception('Skills was not add to group ID:', $group->id);
            }
            $data = ['group_id' => $group->id];
            $status = 201;
        } catch (\Exception $e) {
            $data = [
                'message' => $e->getMessage(),
                'created' => false
            ];
            $status = 422;
        }

        return JsonResponse::respond($data, $status);
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

        try {
            $data = $request->getBody()->getContents();
            $data = json_decode($data, true);
            if (empty($data)) {
                throw new \Exception('JSON is wrong');
            }
            $skills = $data['skills'];
            unset($data['skills']);
            $group = new Group;
            $group->id = $args['group_id'];
            $group->name = $data['name'];
            $group->min_students_num = $data['min_students_num'];
            $group->max_students_num = $data['max_students_num'];
            $group->min_skills_num = $data['min_skills_num'];
            $group->max_skills_num = $data['max_skills_num'];
            $group->max_useless_skill_students
                = $data['max_useless_skill_students'];
            $group->enabled = $data['enabled'];

            $repo = new GroupRepository(new RedisDAO());
            $result = $repo->update($group);
            if (empty($result)) {
                throw new \Exception('Group was not updated.');
            }
            $result = $repo->setSkillsByGroupID($args['group_id'], $skills);
            if (empty($result)) {
                throw new \Exception('Skills were not updated.');
            }
            $data = ['updated' => true, 'group_id' => $args['group_id']];
            $status = 201;
            // @TODO нужна очередь ведь изменение идет группы  и
            // поэтому таблицы groups_users может быть изменена queueproducer->publish(
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
            $repo = new GroupRepository(new RedisDAO());
            // нужна валидация
            $result = $repo->delete($args['group_id']);
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

    public function findTeacher(ServerRequestInterface $request, array $args)
    {
        (new RequestValidator($args))->validate(['group_id' => 'required|numeric']);

        $queueRequest = RequestRepository::createRequest();

        $data = [
            'request_id' => $queueRequest->id,
            'id' => $args['group_id']
        ];

        $producer = new RabbitMQProducer();
        $producer->publish(Worker::COMMAND_FIND_TEACHER, $data);

        return JsonResponse::respond(['id' => $queueRequest->id]);
    }

    public function changeTeacher(ServerRequestInterface $request, array $args)
    {
        (new RequestValidator($args))->validate(['group_id' => 'required|numeric']);

        $queueRequest = RequestRepository::createRequest();

        $data = [
            'request_id' => $queueRequest->id,
            'id' => $args['group_id']
        ];

        $producer = new RabbitMQProducer();
        $producer->publish(Worker::COMMAND_REPLACE_TEACHER, $data);
        return JsonResponse::respond(['result' => $data]);
    }
}
