<?php


namespace App\Validators;

class UserControllerValidator extends BaseControllerValidator
{
    protected const USER_RULES_VALIDATE = [
        'email' => 'required|email',
        'first_name' => 'required',
        'last_name' => 'required',
        'phone' => 'required',
        'enabled' => 'required|boolean',
        'skills' => 'required|array',
    ];
}