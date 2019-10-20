<?php

namespace App\Helpers;

use Phalcon\Config;
use Phalcon\Di\FactoryDefault;

abstract class ProcessHelper
{
    protected static $opts;

    protected $arguments;

    /** @var Config */
    protected $config;

    public function __construct()
    {
        $this->arguments = getopt(implode("", self::getShortOpts()), self::getLongOpts());

        $this->config = FactoryDefault::getDefault()->getShared('config');
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