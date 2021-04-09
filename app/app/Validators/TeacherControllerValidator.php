<?php


namespace App\Validators;

class TeacherControllerValidator extends UserControllerValidator
{
    const TEACHER_RULES_VALIDATE
        = [
            'max_groups_num' => 'required|numeric',
            'min_group_size' => 'required|numeric',
            'max_group_size' => 'required|numeric',
        ];

    const RULE_ARGUMENT = ['teacher_id' => 'required|numeric'];

    public function validateCreate(string $body): void
    {
        $rules = array_merge(self::USER_RULES_VALIDATE, self::TEACHER_RULES_VALIDATE);
        unset($rules['enabled'], $rules['user_id']);
        $this->baseValidateBody($body, $rules);
    }

    public function validateUpdate(string $body, array $args): void
    {
        $this->validateArgument($args);
        $this->baseValidateBody(
            $body,
            array_merge(self::USER_RULES_VALIDATE, self::TEACHER_RULES_VALIDATE)
        );
    }

    public function validateArgument(array $args)
    {
        $this->baseValidateArgument($args, self::RULE_ARGUMENT);
    }
}