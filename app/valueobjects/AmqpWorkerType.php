<?php

namespace App\ValueObjects;

interface AmqpWorkerType
{
    public function getValue() : string;
}