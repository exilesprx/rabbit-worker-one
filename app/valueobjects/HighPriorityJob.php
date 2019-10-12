<?php

namespace App\ValueObjects;

class HighPriorityJob implements JobPriority
{
    private static $value = 1;

    public static function getValue() : int
    {
        return self::$value;
    }
}