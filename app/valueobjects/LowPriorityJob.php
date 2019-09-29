<?php

namespace App\Valueobjects;

class LowPriorityJob
{
    private static $value = 1000;

    public static function getValue() : int
    {
        return self::$value;
    }
}