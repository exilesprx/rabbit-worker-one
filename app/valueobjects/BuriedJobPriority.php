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
     * Set the priority to H * D * M
     *
     * Where:
     *      H = HighPriorityJob value
     *      D = Difference between current version and next version
     *      M = Multiplier
     */
    public static function getValue(OutOfOrderException $exception) : int
    {
        return HighPriorityJob::getValue() * $exception->getDifference() * self::$multiplier;
    }
}