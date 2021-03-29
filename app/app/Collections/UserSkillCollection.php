<?php


namespace App\Collections;


use App\Models\UserSkill;

class UserSkillCollection extends Collection
{

    protected function createObject(array $raw)
    {
        return new UserSkill(
            $raw['id'],
            $raw['user_id'],
            $raw['skill_id'],
        );
    }
}