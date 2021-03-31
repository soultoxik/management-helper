<?php


namespace App\Models\DTOs;

class TeacherConditionDTO
{

    public int $userID;
    public int $maxGroupsNum;
    public int $minGroupSize;
    public int $maxGroupSize;

    public function __construct(
        int $userID,
        int $maxGroupsNum,
        int $minGroupSize,
        int $maxGroupSize
    )
    {
        $this->userID = $userID;
        $this->maxGroupsNum = $maxGroupsNum;
        $this->minGroupSize = $minGroupSize;
        $this->maxGroupSize = $maxGroupSize;
    }
}