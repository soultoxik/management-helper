<?php


namespace App\Helpers;


class ArrayHelper
{
    public static function getColumn(array $array, string $column)
    {
        return array_column($array, $column);
    }

    public static function toArray($data)
    {
        return json_decode(json_encode($data),true);
    }
}