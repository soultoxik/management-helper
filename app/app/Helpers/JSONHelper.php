<?php


namespace App\Helpers;

class JSONHelper
{
    public static function isJSON(string $data): bool
    {
        json_decode($data);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}