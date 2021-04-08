<?php


namespace App\Helpers;


class ArrayHelper
{
    public static function getColumn(array $array, string $column)
    {
        return array_column($array, $column);
    }
}