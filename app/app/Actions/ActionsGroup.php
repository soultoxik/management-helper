<?php


namespace App\Actions;

class ActionsGroup extends Actions
{
    public function addSkills(int $groupID, array $skillIDs)
    {
        foreach ($skillIDs as $skillID) {
            $this->cache->addSkillToGroup($groupID, $skillID);
        }
    }

    public function addStudents(int $groupID, array $studentIDs)
    {

        foreach ($studentIDs as $studentID) {
            $this->cache->addUserToGroup($groupID, $studentID);
        }
    }

    public function delSkills(int $groupID, array $skillIDs)
    {

        foreach ($skillIDs as $skillID) {
            $this->cache->delSkillFromGroup($groupID, $skillID);
        }
    }

    public function delStudents(int $groupID, array $studentIDs)
    {

        foreach ($studentIDs as $studentID) {
            $this->cache->delUserFromGroup($groupID, $studentID);
        }
    }

    public function delGroup(int $groupID)
    {

        $this->cache->delGroup($groupID);
    }
}