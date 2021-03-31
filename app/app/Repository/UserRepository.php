<?php


namespace App\Repository;


use App\Models\User;
use League\Route\Http\Exception\NotFoundException;

class UserRepository
{
    public function findById(int $id): User
    {
        $user = User::find($id);

        if (empty($user)) {
            throw new NotFoundException('user not found');
        }

        return $user;
    }
}