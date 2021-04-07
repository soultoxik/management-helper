<?php


namespace App\Actions;

class ActionsUser extends Actions
{
    public function addSkills(int $userID, array $skillIDs)
    {
        foreach ($skillIDs as $skillID) {
            $this->cache->addSkillToUser($userID, $skillID);
        }
    }

    public function delSkills(int $userID, array $skillIDs)
    {
        foreach ($skillIDs as $skillID) {
            $this->cache->delSkillFromUser($userID, $skillID);
        }
    }

    public function delUserSkill(int $userID)
    {
        $this->cache->delUserSkills($userID);
    }

    public function addGroups(int $userID, array $groupIDs)
    {
        foreach ($groupIDs as $groupID) {
            $this->cache->addUserToGroup($groupID, $userID);
        }
    }

    public function delGroups(int $userID, array $groupIDs)
    {
        foreach ($groupIDs as $groupID) {
            $this->cache->delUserFromGroup($groupID, $userID);
        }
    }
}