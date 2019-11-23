<?php

namespace App\ValueObjects;

class LowPriorityJob implements JobPriority
{
    private static $value = 100;

    public static function getValue() : int
    {
        return self::$value;
    }
}