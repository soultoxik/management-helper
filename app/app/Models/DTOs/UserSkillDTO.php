<?php


namespace App\Models\DTOs;


class UserSkillDTO
{
    public int $id;
    public int $userID;
    public int $skillID;

    public function __construct(int $id, int $userID, int $skillID)
    {
        $this->id = $id;
        $this->userID = $userID;
        $this->skillID = $skillID;
    }
}