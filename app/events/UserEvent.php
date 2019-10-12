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

    public static function getUblName(): string
    {
        $name = static::getEventName();

        preg_match_all("/[A-Z][a-z]+/", $name, $matches, PREG_PATTERN_ORDER);

        return strtolower(implode(".", $matches[0]));
    }

    private static function getEventName() : string
    {
        $name = get_called_class();

        $parts = explode('\\', $name);

        return array_pop($parts);
    }
}