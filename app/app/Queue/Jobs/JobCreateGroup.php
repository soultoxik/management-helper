<?php


namespace App\Queue\Jobs;

use App\Logger\AppLogger;
use App\Models\Group;
use App\Repository\GroupRepository;
use App\Storage\RedisDAO;

class JobCreateGroup extends Job
{
    private Group $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function work(): bool
    {
        $repo = new GroupRepository(new RedisDAO());
        $result = $repo->formGroup($this->group);
        if (empty($result)) {
            AppLogger::addInfo(
                'RabbitMQ:Consumer - Could not find Students for group: ' . $this->group->id
            );
            return false;
        }
        $studentIDs = [];
        foreach ($result as $item) {
            $studentIDs[] = $item->user_id;
        }
        return $repo->setStudentsByGroupID($this->group->id, $studentIDs);
    }


}