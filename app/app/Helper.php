<?php


namespace App;


class Helper
{
    public static function isJSON(string $data): bool
    {
        json_decode($data);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}