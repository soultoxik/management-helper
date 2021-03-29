<?php


namespace App\Models;

class Group
{
    private int $id;
    private string $name;
    private int $userID;
    private int $minStudentsNum;
    private int $maxStudentsNum;
    private int $minSkillsNum;
    private int $maxSkillsNum;
    private int $maxUselessSkillStudents;
    private bool $enabled;
    private int $created;

    public function __construct(
        int $id,
        string $name,
        int $userID,
        int $minStudentsNum,
        int $maxStudentsNum,
        int $minSkillsNum,
        int $maxSkillsNum,
        int $maxUselessSkillStudents,
        bool $enabled,
        int $created
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->userID = $userID;
        $this->minStudentsNum = $minStudentsNum;
        $this->maxStudentsNum = $maxStudentsNum;
        $this->minSkillsNum = $minSkillsNum;
        $this->maxSkillsNum = $maxSkillsNum;
        $this->maxUselessSkillStudents = $maxUselessSkillStudents;
        $this->enabled = $enabled;
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

    public function getMinStudentsNum(): int
    {
        return $this->minStudentsNum;
    }

    public function setMinStudentsNum(int $minStudentsNum): void
    {
        $this->minStudentsNum = $minStudentsNum;
    }

    public function getMaxStudentsNum(): int
    {
        return $this->maxStudentsNum;
    }

    public function setMaxStudentsNum(int $maxStudentsNum): void
    {
        $this->maxStudentsNum = $maxStudentsNum;
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

    public function getMaxUselessSkillStudents(): int
    {
        return $this->maxUselessSkillStudents;
    }

    public function setMaxUselessSkillStudents(
        int $maxUselessSkillStudents
    ): void
    {
        $this->maxUselessSkillStudents = $maxUselessSkillStudents;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function setCreated(int $created): void
    {
        $this->created = $created;
    }

}