<?php

namespace App\ValueObjects;

class ReactorPriority
{
    protected static $value = 50;

    public static function toInteger() : int
    {
        return self::$value;
    }
}