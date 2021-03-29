<?php


namespace App\Collections;

use App\Models\TeacherConditions;

class TeacherConditionsCollection extends Collection
{

    protected function createObject(array $raw)
    {
        return new TeacherConditions(
            $raw['id'],
            $raw['user_id'],
            $raw['max_groups_num'],
            $raw['min_group_size'],
            $raw['max_group_size']
        );
    }

}
