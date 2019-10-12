<?php

namespace App\Events;

interface EventContract
{
    public function getData() : array;

    public static function getEventType() : string;

    public static function getBaseEventType() : string;

    public static function getUblName() : string;
}