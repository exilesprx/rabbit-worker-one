<?php

namespace App\Listeners;

interface ListenerContract
{
    public static function getEvents() : array;
}