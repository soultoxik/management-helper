<?php


namespace App\Models;

class TeacherConditions
{
    private int $id;
    private int $userID;
    private int $maxGroupsNum;
    private int $minGroupSize;
    private int $maxGroupSize;

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

    public function getMaxGroupsNum(): int
    {
        return $this->maxGroupsNum;
    }

    public function setMaxGroupsNum(int $maxGroupsNum): void
    {
        $this->maxGroupsNum = $maxGroupsNum;
    }

    public function getMinGroupSize(): int
    {
        return $this->minGroupSize;
    }

    public function setMinGroupSize(int $minGroupSize): void
    {
        $this->minGroupSize = $minGroupSize;
    }

    public function getMaxGroupSize(): int
    {
        return $this->maxGroupSize;
    }

    public function setMaxGroupSize(int $maxGroupSize): void
    {
        $this->maxGroupSize = $maxGroupSize;
    }

}