<?php


namespace App\Queue\Jobs;

use App\Logger\AppLogger;
use App\Models\Group;
use App\Repository\GroupRepository;
use App\Storage\RedisDAO;

class JobChangeTeacher extends Job
{
    private Group $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function work(): bool
    {
        // тут писать замену препода из числа свободных
        // результат. новое значение поле user_id в таблице groups

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
            . $status . 'changed Teacher:' . $user->id
        );
        return $result;
    }
}