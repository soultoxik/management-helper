<?php


namespace App\Queue\Jobs;

use App\Models\User;
use App\Repository\StudentRepository;

class JobFindGroupNewUser extends Job
{
    private User $student;

    public function __construct(User $student)
    {
        $this->student = $student;
    }

    public function work(): bool
    {
        $student = new StudentRepository($this->student);
        $student->findSuitableGroup();
        $student->addToGroup();

        if ($student->getGroup()) {
            return true;
        }

        return false;
    }
}