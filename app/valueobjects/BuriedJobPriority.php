<?php

namespace App\ValueObjects;

use App\Exceptions\OutOfOrderException;

class BuriedJobPriority
{
    private static $multiplier = 100;

    /**
     * @param OutOfOrderException $exception
     * @return int
     *
     * Set the priority to L + D * M
     *
     * Where:
     *      L = LowPriorityJob value
     *      D = Difference between current version and next version
     *      M = Multiplier
     */
    public static function getValue(OutOfOrderException $exception) : int
    {
        return LowPriorityJob::getValue() + $exception->getVersion() * self::$multiplier;
    }
}