<?php

namespace App\Controllers;

use App\Helpers\JSONHelper;
use App\Models\DTOs\GroupDTO;
use App\Queue\Jobs\Worker;
use App\Queue\RabbitMQProducer;
use App\Repository\GroupRepository;
use App\Repository\RequestRepository;
use App\Response\JsonResponse;
use App\Storage\RedisDAO;
use App\Validators\RequestValidator;
use Psr\Http\Message\ServerRequestInterface;
use App\Models\Group;

class GroupController
{

    const RULES_VALIDATE = [
        'name' => 'required|alpha_spaces',
        'min_students_num' => 'required|numeric',
        'max_students_num' => 'required|numeric',
        'min_skills_num' => 'required|numeric',
        'max_skills_num' => 'required|numeric',
        'max_useless_skill_students' => 'required|numeric',
        'enabled' => 'required|boolean',
        'skills' => 'required|array',
    ];

    public function create(ServerRequestInterface $request, array $args)
    {
        try {
            $body = $request->getBody()->getContents();
            $this->validateCreate($body);

            $body = json_decode($body, true);
            $groupDTO = $this->prepareGroupDTO($body);

            $repo = new GroupRepository(new RedisDAO());
            $group = $repo->create($groupDTO);
            if (empty($group)) {
                throw new \Exception('Group was not create');
            }
            $result = $repo->setSkillsByGroupID($group->id, $body['skills']);
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
        $this->validateArgument($args);
        $repo = new GroupRepository(new RedisDAO());
        $group = $repo->getGroupFull($args['group_id']);
        $data = [
            'group' => $group->toArray(),
            'skills' => $group->getSkills()->pluck('id')->toArray(),
            'students' => $group->getStudents()->pluck('id')->toArray(),
        ];
        return JsonResponse::respond($data);
    }

    public function update(ServerRequestInterface $request, array $args)
    {
        try {
            $body = $request->getBody()->getContents();
            $this->validateUpdate($body, $args);

            $body = json_decode($body, true);
            $group = $this->prepareGroup($args['group_id'], $body);

            $repo = new GroupRepository(new RedisDAO());
            $result = $repo->update($group);
            if (empty($result)) {
                throw new \Exception('Group was not updated.');
            }
            $result = $repo->setSkillsByGroupID($args['group_id'], $body['skills']);
            if (empty($result)) {
                throw new \Exception('Skills were not updated.');
            }
            $data = ['updated' => true, 'group_id' => $args['group_id']];
            $status = 201;
//            // @TODO нужна очередь ведь изменение идет группы  и
//            // поэтому таблицы groups_users может быть изменена queueproducer->publish(
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
            $repo = new GroupRepository(new RedisDAO());
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

    public function formGroup(ServerRequestInterface $request, array $args)
    {
        (new RequestValidator($args))->validate(['group_id' => 'required|numeric']);

        $queueRequest = RequestRepository::createRequest();

        $data = [
            'request_id' => $queueRequest->id,
            'id' => $args['group_id']
        ];

        $producer = new RabbitMQProducer();
        $producer->publish(Worker::COMMAND_CREATE_GROUP, $data);
        return JsonResponse::respond(['result' => $data]);
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
        $validator->validate(['group_id' => 'required|numeric']);
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

    private function prepareGroupDTO(array $body): GroupDTO
    {
        return new GroupDTO(
            $body['name'],
            null,
            $body['min_students_num'],
            $body['max_students_num'],
            $body['min_skills_num'],
            $body['max_skills_num'],
            $body['max_useless_skill_students'],
            false
        );
    }

    private function prepareGroup(int $groupID, array $body): Group
    {
        $properties = array_keys(self::RULES_VALIDATE);

        $group = new Group;
        $group->id = $groupID;
        foreach ($properties as $item) {
            if ($item == 'skills') {
                continue;
            }
            $group->{$item} = $body[$item];
        }

        return $group;
    }
}
