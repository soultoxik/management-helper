<?php


namespace App\Queue\Jobs;

use App\Logger\AppLogger;
use App\Repository\GroupRepository;
use App\Repository\StudentRepository;
use App\Models\Student;
use App\Storage\RedisDAO;

class JobFindGroupNewUser extends Job
{
    const FAIL = 'not_found_group_for_user';

    private Student $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    protected function work(): bool
    {
        $repo = new StudentRepository(new RedisDAO());
        $group = $repo->findSuitableGroup($this->student->user);
        if (empty($group)) {
            AppLogger::addInfo(
                'RabbitMQ:Consumer - Could not find groups for student: ' . $this->student->user->id
            );
            return false;
        }
        $result = $repo->addGroup($this->student->user->id, $group->id);
        $status = $result ? ' was ': ' was not ';
        AppLogger::addInfo(
            'RabbitMQ:Consumer - For student: ' . $this->student->user->id
            . $status . 'found groupID:' . $group->id
        );
        return $result;
    }

    public function getStatusFail(): string
    {
        return self::FAIL;
    }
}