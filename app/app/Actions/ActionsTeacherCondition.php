<?php


namespace App\Actions;

use App\Models\TeacherCondition;

class ActionsTeacherCondition extends Actions
{
    public function delete(TeacherCondition $teacherCondition)
    {
        $this->cache->delTeacherConditionByID($teacherCondition->id);
    }

    public function save(TeacherCondition $teacherCondition)
    {
        $this->cache->setTeacherCondition($teacherCondition);
    }
}