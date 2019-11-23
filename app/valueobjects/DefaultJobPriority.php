<?php

namespace App\ValueObjects;

class DefaultJobPriority
{
    private static $multiplier = 100;

    /**
     * @param int $offset
     * @return int
     *
     * Set the priority to L + D * M
     *
     * Where:
     *      L = LowPriorityJob value
     *      O = Offset, which is the version of the event
     *      M = Multiplier
     */
    public static function getValue(int $offset) : int
    {
        return LowPriorityJob::getValue() + $offset * self::$multiplier;
    }
}