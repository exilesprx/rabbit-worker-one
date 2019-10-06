<?php

namespace App\Helpers;

abstract class ProcessHelper
{
    protected static $opts;

    protected $arguments;

    public function __construct()
    {
        $this->arguments = getopt(implode("", self::getShortOpts()), self::getLongOpts());
    }

    protected static function getShortOpts() : array
    {
        return array_keys(static::$opts);
    }

    protected static function getLongOpts() : array
    {
        return array_values(static::$opts);
    }
}