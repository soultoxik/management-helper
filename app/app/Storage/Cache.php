<?php


namespace App\Storage;


use App\Models\Group;
use App\Models\TeacherCondition;

interface Cache
{
    public function getGroup(int $groupID): ?Group;
    public function setGroup(Group $group): bool;
    public function setGroupSkills(int $groupID, array $skillIDs): bool;
    public function getGroupSkills(int $groupID): ?array;
    public function setGroupUsers(int $groupID, array $userIDs): bool;
    public function getGroupUsers(int $groupID): ?array;
    public function setTeacherCondition(TeacherCondition $teacherCondition): bool;
    public function getTeacherConditionByID(int $id): ?TeacherCondition;
    public function getTeacherConditionByUserID(int $userID): ?TeacherCondition;
    public function setUserSkills(int $userID, array $skillIDs): bool;
    public function getUserSkills(int $userID): array;
}