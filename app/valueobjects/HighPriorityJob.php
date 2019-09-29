<?php


namespace App\Valueobjects;

class HighPriorityJob
{
    private static $value = 1;

    public static function getValue() : int
    {
        return self::$value;
    }
}