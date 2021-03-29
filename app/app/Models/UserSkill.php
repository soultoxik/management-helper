<?php


namespace App\Models;

class UserSkill
{
    private int $id;
    private int $userID;
    private int $skillID;

    public function __construct(int $id, int $userID, int $skillID)
    {
        $this->id = $id;
        $this->userID = $userID;
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

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function setUserID(int $userID): void
    {
        $this->userID = $userID;
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