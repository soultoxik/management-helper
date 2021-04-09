<?php


namespace App\Validators;

class RequestControllerValidator extends BaseControllerValidator implements ControllerValidatorInterface
{
    const RULE_ARGUMENT = ['request_id' => 'required|numeric'];

    public function validateArgument(array $args): void
    {
        $this->baseValidateArgument($args, self::RULE_ARGUMENT);
    }

    public function validateCreate(string $body): void
    {
    }

    public function validateUpdate(string $body, array $args): void
    {
    }
}