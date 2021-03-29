<?php


namespace App\Models;

class Group
{
    private int $id;
    private string $name;
    private int $userID;
    private int $minPupilNum;
    private int $maxPupilNum;
    private int $minSkillsNum;
    private int $maxSkillsNum;
    private int $maxUslessSkillPupil;
    private bool $status;
    private int $created;

    public function __construct(
        int $id,
        string $name,
        int $userID,
        int $minPupilNum,
        int $maxPupilNum,
        int $minSkillsNum,
        int $maxSkillsNum,
        int $maxUslessSkillPupil,
        bool $status,
        int $created
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->userID = $userID;
        $this->minPupilNum = $minPupilNum;
        $this->maxPupilNum = $maxPupilNum;
        $this->minSkillsNum = $minSkillsNum;
        $this->maxSkillsNum = $maxSkillsNum;
        $this->maxUslessSkillPupil = $maxUslessSkillPupil;
        $this->status = $status;
        $this->created = $created;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function setCreated(int $created): void
    {
        $this->created = $created;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function setUserID(int $userID): void
    {
        $this->userID = $userID;
    }

    public function getMinPupilNum(): int
    {
        return $this->minPupilNum;
    }

    public function setMinPupilNum(int $minPupilNum): void
    {
        $this->minPupilNum = $minPupilNum;
    }

    public function getMaxPupilNum(): int
    {
        return $this->maxPupilNum;
    }

    public function setMaxPupilNum(int $maxPupilNum): void
    {
        $this->maxPupilNum = $maxPupilNum;
    }

    public function getMinSkillsNum(): int
    {
        return $this->minSkillsNum;
    }

    public function setMinSkillsNum(int $minSkillsNum): void
    {
        $this->minSkillsNum = $minSkillsNum;
    }

    public function getMaxSkillsNum(): int
    {
        return $this->maxSkillsNum;
    }

    public function setMaxSkillsNum(int $maxSkillsNum): void
    {
        $this->maxSkillsNum = $maxSkillsNum;
    }

    public function getMaxUslessSkillPupil(): int
    {
        return $this->maxUslessSkillPupil;
    }

    public function setMaxUslessSkillPupil(int $maxUslessSkillPupil): void
    {
        $this->maxUslessSkillPupil = $maxUslessSkillPupil;
    }

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

}