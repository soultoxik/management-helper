<?php


namespace App\Repository;

use App\Models\DTOs\TeacherConditionDTO;
use App\Models\Teacher;
use App\Models\TeacherCondition;
use App\Models\User;
use App\Repository\Traits\CacheTrait;

class TeacherRepository
{
    use CacheTrait;

    public function getTeacherByID(int $teacherID): ?Teacher
    {
        return Teacher::findByID($teacherID);
    }

    public function getTeacherByEmail(string $email): ?Teacher
    {
        return Teacher::findByEmail($email);
    }

    public function create(
        User $user,
        array $skills,
        TeacherConditionDTO $conditionDTO
    ): ?Teacher
    {
        return Teacher::insert($user, $skills, $conditionDTO);
    }

    public function update(
        User $user,
        array $skills,
        TeacherCondition $condition
    ): bool
    {
        return Teacher::change($user, $skills, $condition);
    }

    public function delete(int $userID): bool
    {
        return Teacher::remove($userID);
    }
}