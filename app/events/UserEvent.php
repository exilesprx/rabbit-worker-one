<?php

namespace App\Events;

abstract class UserEvent implements EventContract
{
    private static $baseEventType = "user-update";

    public static function getBaseEventType() : string
    {
        return self::$baseEventType;
    }

    public static function getEventType() : string
    {
        return sprintf("%s:on%s", self::$baseEventType, static::getEventName());
    }

    private static function getEventName() : string
    {
        $name = get_called_class();

        $parts = explode('\\', $name);

        return array_pop($parts);
    }
}