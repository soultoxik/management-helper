<?php


namespace App\Validators;

class GroupControllerValidator extends BaseControllerValidator implements ControllerValidatorInterface
{
    const RULES_BODY = [
        'name' => 'required|alpha_spaces',
        'min_students_num' => 'required|numeric',
        'max_students_num' => 'required|numeric',
        'min_skills_num' => 'required|numeric',
        'max_skills_num' => 'required|numeric',
        'max_useless_skill_students' => 'required|numeric',
        'enabled' => 'required|boolean',
        'skills' => 'required|array',
    ];
    const RULE_ARGUMENT = ['group_id' => 'required|numeric'];

    public function validateCreate(string $body): void
    {
        $rules = self::RULES_BODY;
        unset($rules['enabled']);
        $this->baseValidateBody($body, $rules);
    }

    public function validateUpdate(string $body, array $args): void
    {
        $this->validateArgument($args);
        $this->baseValidateBody($body, self::RULES_BODY);
    }

    public function validateArgument(array $args): void
    {
        $this->baseValidateArgument($args, self::RULE_ARGUMENT);
    }
}
