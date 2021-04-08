<?php


namespace App\Queue\Jobs;

use App\Models\Student;

class JobFindGroupNewUser extends Job
{
    private Student $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function work(): bool
    {
        // тут писать подбор группы для студента по его критериям
        // результат. добавление записи в таблицу groups_users
    }
}