<?php


namespace App\Queue\Jobs;


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