<?php

namespace App\Helpers;

use Phalcon\Config\Factory;

abstract class ProcessHelper
{
    protected static $opts;

    protected $arguments;

    protected $config;

    public function __construct()
    {
        $this->arguments = getopt(implode("", self::getShortOpts()), self::getLongOpts());

        $this->config = Factory::load(
            [
                'filePath' => APP_PATH . "/Config/config.php",
                'adapter' => 'php'
            ]
        );
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