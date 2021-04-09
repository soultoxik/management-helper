<?php


namespace App\Controllers;

use App\Models\User;

trait UserControllerTrait
{

    protected function prepareCreateUser(array $body, bool $isTeacher): User
    {
        $user = $this->prepareUser($body);
        $user->enabled = true;
        $user->teacher = $isTeacher;
        return $user;
    }

    protected function prepareUpdateUser(int $studentID, array $body, bool $isTeacher): User
    {
        $user = $this->prepareUser($body);
        $user->id = $studentID;
        $user->teacher = $isTeacher;
        $user->enabled = $body['enabled'];
        return $user;
    }

    private function prepareUser(array $body): User
    {
        $user = new User;
        $user->email = $body['email'];
        $user->first_name = $body['first_name'];
        $user->last_name = $body['last_name'];
        $user->phone = $body['phone'];
        return $user;
    }
}
