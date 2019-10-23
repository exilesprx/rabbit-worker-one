<?php

namespace App\Events;

trait EmailEvent
{
    protected static $baseEventType = "email-validation";

    /**
     * @return string
     */
    public static function getBaseEventType(): string
    {
        return self::$baseEventType;
    }
}