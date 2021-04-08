<?php


namespace App\Validators;


use League\Route\Http\Exception\BadRequestException;
use Rakit\Validation\Validator;

class RequestValidator implements ValidatorInterface
{
    private Validator $validator;
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->validator = new Validator();
    }

    /**
     * @param array $rules
     * @throws BadRequestException
     */
    public function validate(array $rules)
    {
        $validation = $this->validator->validate($this->data, $rules);

        if ($validation->fails()) {
            throw new BadRequestException(json_encode($validation->errors()->firstOfAll()));
        }
    }
}