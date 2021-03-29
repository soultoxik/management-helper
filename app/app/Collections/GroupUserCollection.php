<?php


namespace App\Collections;

use App\Models\GroupUser;

class GroupUserCollection
{
    protected function createObject(array $raw)
    {
        return new GroupUser($raw['id'], $raw['group_id'], $raw['user_id']);
    }
}