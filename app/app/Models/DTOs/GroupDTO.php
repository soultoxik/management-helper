<?php


namespace App\Models\DTOs;


class GroupDTO
{
    public int $id;
    public string $name;
    public int $userID;
    public int $minPupilNum;
    public int $maxPupilNum;
    public int $minSkillsNum;
    public int $maxSkillsNum;
    public int $maxUslessSkillPupil;
    public bool $status;
    public int $created;

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
}