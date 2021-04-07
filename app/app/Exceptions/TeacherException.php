<?php


namespace App\Exceptions;

use League\Route\Http\Exception;

class TeacherException extends AppException
{
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($code, $message, $previous);
    }
}