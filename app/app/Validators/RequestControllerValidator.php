<?php


namespace App\Validators;


class RequestControllerValidator extends BaseControllerValidator
{
    const RULE_ARGUMENT = ['request_id' => 'required|numeric'];

    public function validateArgument(array $args)
    {
        $this->baseValidateArgument($args, self::RULE_ARGUMENT);
    }
}