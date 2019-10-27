<?php

namespace App\StateMachines;

trait EmailValidationTrait
{
    public function __toString(): string
    {
        return static::$status;
    }

    public static function equals(string $status): bool
    {
        return $status == static::$status;
    }
}