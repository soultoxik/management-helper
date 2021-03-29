<?php


namespace App\Collections;

use App\Models\User;

class UserCollection extends Collection
{
    public function createObject(array $raw)
    {
        return new User(
            $raw['id'],
            $raw['email'],
            $raw['first_name'],
            $raw['last_name'],
            $raw['phone'],
            $raw['status'],
            $raw['teacher'],
            $raw['created'],
        );
    }
}
