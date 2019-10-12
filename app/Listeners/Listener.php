<?php

namespace App\Listeners;

abstract class Listener implements ListenerContract
{
    protected static $events = [];

    public static function getEvents() : array
    {
        return static::$events;
    }
}