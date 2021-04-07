<?php


namespace App\Queue\Jobs;


use App\Models\Group;

class JobFindTeacher extends Job
{
    private Group $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function work(): bool
    {
        // тут писать подбор преподавателя для группы
        // результат. заполненое поле user_id в таблице groups
    }
}