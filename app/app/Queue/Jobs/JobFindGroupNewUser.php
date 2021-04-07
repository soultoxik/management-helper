<?php


namespace App\Queue\Jobs;

use App\Repository\GroupRepository;
use App\Repository\StudentRepository;
use App\Models\Student;
use App\Storage\RedisDAO;

class JobFindGroupNewUser extends Job
{
    private Student $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function work(): bool
    {
        $repo = new StudentRepository();
        $repo->setRedis(new RedisDAO());
        $group = $repo->findSuitableGroup($this->student->user);

        $groupRepo = new GroupRepository();
        $groupRepo->setRedis(new RedisDAO());
        $groupRepo->setStudentsByGroupID($group->id, [$this->student->user->id]);

        if ($repo->getGroup()) {
            return true;
        }

        return false;
    }
}