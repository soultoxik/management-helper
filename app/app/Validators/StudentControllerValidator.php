<?php


namespace App\Validators;

class StudentControllerValidator extends UserControllerValidator implements ControllerValidatorInterface
{
    const RULE_ARGUMENT = ['student_id' => 'required|numeric'];

    public function validateCreate(string $body): void
    {
        $rules = self::USER_RULES_VALIDATE;
        unset($rules['enabled']);
        $this->baseValidateBody($body, $rules);
    }

    public function validateUpdate(string $body, array $args): void
    {
        $this->validateArgument($args);
        $this->baseValidateBody($body, self::USER_RULES_VALIDATE);
    }

    public function validateArgument(array $args): void
    {
        $this->baseValidateArgument($args, self::RULE_ARGUMENT);
    }
}