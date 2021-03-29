<?php


namespace App\Collections;

use App\Models\Group;

class GroupCollection extends Collection
{

    protected function createObject(array $raw)
    {
        return new Group(
            $raw['id'],
            $raw['name'],
            $raw['user_id'],
            $raw['min_pupil_num'],
            $raw['max_pupil_num'],
            $raw['min_skills_num'],
            $raw['max_skills_num'],
            $raw['max_useless_kills_pupil'],
            $raw['active'],
            $raw['created'],
        );
    }
}