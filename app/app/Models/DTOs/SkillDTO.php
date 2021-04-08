<?php


namespace App\Models\DTOs;

class SkillDTO
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}