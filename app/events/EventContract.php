<?php

namespace App\Events;

interface EventContract
{
    public function getData() : array;

    public function getEventType() : string;
}