<?php

namespace App\ValueObjects;

class QueueStandardTimeout
{
    private static $TIMEOUT = 60;

    public static function getValue() : int
    {
        return self::$TIMEOUT;
    }
}