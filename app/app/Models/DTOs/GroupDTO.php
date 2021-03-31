<?php


namespace App\Models\DTOs;


class GroupDTO
{

    public string $name;
    public ?int $userID;
    public int $minStudentsNum;
    public int $maxStudentsNum;
    public int $minSkillsNum;
    public int $maxSkillsNum;
    public float $maxUselessSkillStudents;
    public bool $enabled;


    public function __construct(
        string $name,
        ?int $userID,
        int $minStudentsNum,
        int $maxStudentsNum,
        int $minSkillsNum,
        int $maxSkillsNum,
        float $maxUselessSkillStudents,
        int $enabled
    )
    {
        $this->name = $name;
        $this->userID = $userID;
        $this->minStudentsNum = $minStudentsNum;
        $this->maxStudentsNum = $maxStudentsNum;
        $this->minSkillsNum = $minSkillsNum;
        $this->maxSkillsNum = $maxSkillsNum;
        $this->maxUselessSkillStudents = $maxUselessSkillStudents;
        $this->enabled = $enabled;
    }
}