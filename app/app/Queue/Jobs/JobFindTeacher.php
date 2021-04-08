<?php


namespace App\Queue\Jobs;


use App\Logger\AppLogger;
use App\Models\Group;
use App\Repository\GroupRepository;
use App\Storage\RedisDAO;

class JobFindTeacher extends Job
{
    const FAIL = 'not_found_teacher';

    private Group $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    protected function work(): bool
    {
        $repo = new GroupRepository(new RedisDAO());
        $user = $repo->findSuitableTeacher($this->group);
        if (empty($user)) {
            AppLogger::addInfo(
                'RabbitMQ:Consumer - Could not find Teacher for group: ' . $this->group->id
            );
            return false;
        }
        $result = $repo->setTeacherID($this->group, $user->id);
        $status = $result ? ' was ': ' was not ';
        AppLogger::addInfo(
            'RabbitMQ:Consumer - For Group: ' . $this->group->id
            . $status . 'found Teacher:' . $user->id
        );
        return $result;
    }

    public function getStatusFail(): string
    {
        return self::FAIL;
    }
}