<?php


namespace App\Models;

class GroupSkill
{
    private int $id;
    private int $groupID;
    private int $skillID;

    public function __construct(int $id, int $groupID, int $skillID)
    {
        $this->id = $id;
        $this->groupID = $groupID;
        $this->skillID = $skillID;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getGroupID(): int
    {
        return $this->groupID;
    }

    public function setGroupID(int $groupID): void
    {
        $this->groupID = $groupID;
    }

    public function getSkillID(): int
    {
        return $this->skillID;
    }

    public function setSkillID(int $skillID): void
    {
        $this->skillID = $skillID;
    }


}