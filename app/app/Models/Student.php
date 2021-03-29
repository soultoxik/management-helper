<?php


namespace App\Models;

use App\Collections\SkillCollection;

class Student
{
    private User $user;
    private SkillCollection $skillCollection;

    public function __construct(User $user, SkillCollection $skillCollection)
    {
        $this->user = $user;
        $this->skillCollection = $skillCollection;
    }
}