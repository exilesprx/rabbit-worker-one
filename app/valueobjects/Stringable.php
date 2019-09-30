<?php

namespace App\ValueObjects;

interface Stringable
{
    public function __toString() : string;
}