<?php


namespace App\Models;

use App\Collections\SkillCollection;

class Teacher
{
    private User $user;
    private SkillCollection $skillCollection;
    private TeacherConditions $teacherConditions;

    public function __construct(
        User $user,
        SkillCollection $skillCollection,
        TeacherConditions $teacherConditions
    )
    {
        $this->user = $user;
        $this->skillCollection = $skillCollection;
        $this->teacherConditions = $teacherConditions;
    }
}