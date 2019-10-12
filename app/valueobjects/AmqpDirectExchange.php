<?php

namespace App\ValueObjects;

class AmqpDirectExchange implements AmqpWorkerType, Stringable
{
    private static $TYPE = "direct";

    public static function getType() : string
    {
        return self::$TYPE;
    }

    public function getValue(): string
    {
        return self::$TYPE;
    }

    public function __toString(): string
    {
        return self::$TYPE;
    }
}