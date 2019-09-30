<?php

namespace App\ValueObjects;

interface JobPriority
{
    public static function getValue() : int;
}