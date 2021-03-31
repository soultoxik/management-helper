<?php


namespace App\Repository;


use App\Models\User;

class StudentRepository
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function findSuitableGroup()
    {
        return $this->user->skills()->get();
    }
}