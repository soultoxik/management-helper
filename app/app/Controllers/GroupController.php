<?php

namespace App\Controllers;

use App\Models\DTOs\GroupDTO;
use App\Queue\Jobs\Worker;
use App\Repository\GroupRepository;
use App\Response\JsonResponse;
use App\Validators\GroupControllerValidator;
use Psr\Http\Message\ServerRequestInterface;
use App\Models\Group;

class GroupController extends Controller
{

    protected GroupRepository $groupRepo;

    public function __construct()
    {
        parent::__construct();
        $this->validator = new GroupControllerValidator();
        $this->groupRepo = new GroupRepository($this->redis);
    }

    public function create(ServerRequestInterface $request, array $args)
    {
        try {
            $body = $request->getBody()->getContents();
            $this->validator->validateCreate($body);

            $body = json_decode($body, true);
            $groupDTO = new GroupDTO(
                $body['name'],
                null,
                $body['min_students_num'],
                $body['max_students_num'],
                $body['min_skills_num'],
                $body['max_skills_num'],
                $body['max_useless_skill_students'],
                false
            );

            $group = $this->groupRepo->create($groupDTO);
            if (empty($group)) {
                throw new \Exception('Group was not create');
            }
            $result = $this->groupRepo->setSkillsByGroupID($group->id, $body['skills']);
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
        $this->validator->validateArgument($args);

        $group = $this->groupRepo->getGroupFull($args['group_id']);
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

            $this->validator->validateUpdate($body, $args);

            $body = json_decode($body, true);

            $group = new Group;
            $group->id = $args['group_id'];
            $group->name = $body['name'];
            $group->min_students_num = $body['min_students_num'];
            $group->max_students_num = $body['max_students_num'];
            $group->min_skills_num = $body['min_skills_num'];
            $group->max_skills_num = $body['max_skills_num'];
            $group->max_useless_skill_students = $body['max_useless_skill_students'];
            $group->enabled = $body['enabled'];

            $result = $this->groupRepo->update($group);
            if (empty($result)) {
                throw new \Exception('Group was not updated.');
            }
            $result = $this->groupRepo->setSkillsByGroupID($args['group_id'], $body['skills']);
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
            $this->validator->validateArgument($args);

            $result = $this->groupRepo->delete($args['group_id']);
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
        $this->validator->validateArgument($args);
        $data = $this->asyncRequest(
            $args['group_id'],
            Worker::COMMAND_FIND_TEACHER
        );
        return JsonResponse::respond($data);
    }

    public function changeTeacher(ServerRequestInterface $request, array $args)
    {
        $this->validator->validateArgument($args);
        $data = $this->asyncRequest(
            $args['group_id'],
            Worker::COMMAND_REPLACE_TEACHER
        );
        return JsonResponse::respond($data);
    }

    public function formGroup(ServerRequestInterface $request, array $args)
    {
        $this->validator->validateArgument($args);
        $data = $this->asyncRequest(
            $args['group_id'],
            Worker::COMMAND_CREATE_GROUP
        );
        return JsonResponse::respond($data);
    }
}
