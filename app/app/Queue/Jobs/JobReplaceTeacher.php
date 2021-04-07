<?php


namespace App\Queue\Jobs;


use App\Models\Group;

class JobReplaceTeacher extends Job
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
    }
}