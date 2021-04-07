<?php


namespace App\Queue\Jobs;

use App\Models\Group;

class JobCreateGroup extends Job
{
    private Group $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function work(): bool
    {
        // тут писать поиск студентов которые подходят для группы
        // результат. добавление записей в таблицу groups_users
    }


}