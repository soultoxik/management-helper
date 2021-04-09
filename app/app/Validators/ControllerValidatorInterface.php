<?php


namespace App\Validators;

interface ControllerValidatorInterface
{
    public function validateCreate(string $body): void;
    public function validateUpdate(string $body, array $args): void;
    public function validateArgument(array $args): void;
}