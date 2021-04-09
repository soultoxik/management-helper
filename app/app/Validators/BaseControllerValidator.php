<?php


namespace App\Validators;

use App\Helpers\JSONHelper;

class BaseControllerValidator
{
    protected function baseValidateBody(string $body, array $rules)
    {
        if (!JSONHelper::isJSON($body)) {
            throw new \Exception('Received string is not in JSON-format.');
        }
        $data = json_decode($body, true);
        $validator = new RequestValidator($data);
        $validator->validate($rules);
    }

    protected function baseValidateArgument(array $args, array $rule)
    {
        $validator = new RequestValidator($args);
        $validator->validate($rule);
    }
}
