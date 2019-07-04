<?php

namespace App\Events;

abstract class UserEvent implements EventContract
{
    private static $baseEventType = "user-update";

    public function getEventType() : string
    {
        return sprintf("%s:%s", self::$baseEventType, static::getEventName());
    }

    private static function getEventName() : string
    {
        $name = get_called_class();

        $parts = explode('\\', $name);

        return array_pop($parts);
    }
}