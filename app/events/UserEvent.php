<?php

namespace App\Events;

trait UserEvent
{
    protected static $baseEventType = "user-update";

    /**
     * @return string
     */
    public static function getBaseEventType(): string
    {
        return self::$baseEventType;
    }
}