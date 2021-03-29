<?php


namespace App\Models\DTOs;


class TeacherConditionsDTO
{
    public int $id;
    public int $userID;
    public int $maxGroupsNum;
    public int $minGroupSize;
    public int $maxGroupSize;

    public function __construct(
        int $id,
        int $userID,
        int $maxGroupsNum,
        int $minGroupSize,
        int $maxGroupSize
    )
    {
        $this->id = $id;
        $this->userID = $userID;
        $this->maxGroupsNum = $maxGroupsNum;
        $this->minGroupSize = $minGroupSize;
        $this->maxGroupSize = $maxGroupSize;
    }
}