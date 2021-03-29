<?php


namespace App\Models\DTOs;

class GroupUserDTO
{
    public int $id;
    public int $groupID;
    public int $userID;

    public function __construct(int $id, int $groupID, int $userID)
    {
        $this->id = $id;
        $this->groupID = $groupID;
        $this->userID = $userID;
    }
}