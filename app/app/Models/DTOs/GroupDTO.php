<?php


namespace App\Models\DTOs;

class GroupDTO
{
    public int $id;
    public string $name;
    public int $userID;
    public int $minStudentsNum;
    public int $maxStudentsNum;
    public int $minSkillsNum;
    public int $maxSkillsNum;
    public int $maxUselessSkillStudents;
    public bool $enabled;
    public int $created;

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
}