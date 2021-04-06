<?php


namespace App\Repository;

use App\Models\DTOs\GroupDTO;
use App\Models\Group;
use App\Repository\Traits\CacheTrait;

class GroupRepository
{
    use CacheTrait;

    public function getGroup(int $groupID): ?Group
    {
        $group = $this->cache->getGroup($groupID);
        if (!empty($group)) {
            $group->id = $groupID;
            return $group;
        }
        $group = Group::where('id', $groupID)->first();
        if (empty($group)) {
            return null;
        }
        $this->cache->setGroup($group);
        return $group;
    }

    public function getGroupFull(int $groupID): ?Group
    {
        $group = $this->getGroup($groupID);
        if (empty($groupID)) {
            return null;
        }
        $group->setStudents($group->students()->get());
        $group->setSkills($group->skills()->get());
        return $group;
    }

    public function create(GroupDTO $groupDTO): ?Group
    {
        $group = Group::insert($groupDTO);
        if (!empty($group)) {
            $this->cache->setGroup($group);
        }
        return $group;
    }

    public function update(Group $newGroup): bool
    {
        $result = Group::change($newGroup);
        if ($result) {
            $this->cache->setGroup($newGroup);
        }
        return $result;
    }

    public function delete(int $groupID): bool
    {
        return Group::remove($groupID);
    }

    public function getSkillIDsByGroupID(int $groupID): ?array
    {
        $skills = $this->cache->getGroupSkills($groupID);
        if (!empty($skills)) {
            return $skills;
        }
        $group = Group::where('id', $groupID)->first();
        $skills = $group->skills;
        $skillIDs = [];
        foreach ($skills as $item) {
            $skillIDs[] = $item;
        }
        if (empty($skillIDs)) {
            return null;
        }
        $this->cache->setGroupSkills($groupID, $skillIDs);
        return $skillIDs;
    }

    public function getStudentIDsByGroupID(int $groupID): ?array
    {
        $students = $this->cache->getGroupUsers($groupID);
        if (!empty($students)) {
            return $students;
        }
        $group = Group::where('id', $groupID)->first();
        $students = $group->students;
        $studentIDs = [];
        foreach ($students as $item) {
            $studentIDs[] = $item->id;
        }
        if (empty($studentIDs)) {
            return null;
        }
        $this->cache->setGroupUsers($groupID, $studentIDs);
        return $studentIDs;
    }

    public function getTeacherIDByGroupID(int $groupID): ?int
    {
        $group = $this->cache->getGroup($groupID);
        if (isset($group->user_id)) {
            return $group->user_id;
        }
        $group = Group::where('id', $groupID)->first();
        if (empty($group)) {
            return null;
        }
        return $group->user_id;
    }

    public function setSkillsByGroupID(int $groupID, array $skillIDs): bool
    {
        $group = Group::where('id', $groupID)->first();
        if (empty($group)) {
            return false;
        }

        $result = $group->skills()->sync($skillIDs);
        if (empty($result)) {
            return false;
        }

        return true;
    }

    public function setStudentsByGroupID(int $groupID, array $studentIDs): bool
    {
        $group = Group::where('id', $groupID)->first();
        if (empty($group)) {
            return false;
        }

        $result = $group->students()->sync($studentIDs);
        if (empty($result)) {
            return false;
        }
        return true;
    }
}