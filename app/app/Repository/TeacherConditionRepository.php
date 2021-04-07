<?php


namespace App\Repository;

use App\Models\DTOs\TeacherConditionDTO;
use App\Models\TeacherCondition;
use App\Repository\Traits\CacheTrait;

class TeacherConditionRepository
{
    use CacheTrait;

    public function getTeacherConditionByID(int $id): ?TeacherCondition
    {
        $teacherCondition = $this->cache->getTeacherConditionByID($id);
        if (!empty($teacherCondition)) {
            return $teacherCondition;
        }
        $teacherCondition = TeacherCondition::where('id', $id)->first();
        if (empty($teacherCondition)) {
            return null;
        }
        $this->cache->setTeacherCondition($teacherCondition);
        return $teacherCondition;
    }

    public function getTeacherConditionByUserID(int $userID): ?TeacherCondition
    {
        $teacherCondition = $this->cache->getTeacherConditionByUserID($userID);
        if (!empty($teacherCondition)) {
            return $teacherCondition;
        }
        $teacherCondition = TeacherCondition::where('user_id', $userID)->first();
        if (empty($teacherCondition)) {
            return null;
        }
        $this->cache->setTeacherCondition($teacherCondition);
        return $teacherCondition;
    }

    public function create(TeacherConditionDTO $teacherConditionDTO): ?TeacherCondition
    {
        $teacherCondition = TeacherCondition::insert($teacherConditionDTO);
        if (!empty($teacherCondition)) {
            $this->cache->setTeacherCondition($teacherCondition);
        }
        return $teacherCondition;
    }

    public function update(TeacherCondition $newTeacherCondition): bool
    {
        $result = TeacherCondition::change($newTeacherCondition);
        if ($result) {
            $this->cache->setTeacherCondition($newTeacherCondition);
        }
        return $result;
    }

    public function delete(int $id): bool
    {
        return TeacherCondition::remove($id);
    }
}