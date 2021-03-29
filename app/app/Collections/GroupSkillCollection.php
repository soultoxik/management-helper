<?php


namespace App\Collections;

use App\Models\GroupSkill;

class GroupSkillCollection
{
    protected function createObject(array $raw)
    {
        return new GroupSkill($raw['id'], $raw['group_id'], $raw['skill_id']);
    }
}