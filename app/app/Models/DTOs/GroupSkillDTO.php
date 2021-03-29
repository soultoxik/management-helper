<?php


namespace App\Models\DTOs;

class GroupSkillDTO
{
    public int $id;
    public int $groupID;
    public int $skillID;

    public function __construct(int $id, int $groupID, int $skillID)
    {
        $this->id = $id;
        $this->groupID = $groupID;
        $this->skillID = $skillID;
    }
}