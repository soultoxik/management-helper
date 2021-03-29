<?php


namespace App\Models;

class GroupUser
{
    private int $id;
    private int $groupID;
    private int $userID;

    public function __construct(int $id, int $groupID, int $userID)
    {
        $this->id = $id;
        $this->groupID = $groupID;
        $this->userID = $userID;
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

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function setUserID(int $userID): void
    {
        $this->userID = $userID;
    }
}
