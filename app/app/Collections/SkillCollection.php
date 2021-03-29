<?php


namespace App\Collections;

use App\Models\Skill;

class SkillCollection extends Collection
{
    public function createObject(array $raw)
    {
        return new Skill($raw['id'], $raw['name']);
    }

}