<?php

namespace App\ValueObjects;

class ListenerPriority
{
    protected static $value = 100;

    public static function toInteger() : int
    {
        return self::$value;
    }
}