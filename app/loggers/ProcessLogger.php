<?php

namespace App\Loggers;

use Phalcon\Config;
use Phalcon\Logger\Adapter\File;

abstract class ProcessLogger
{
    protected static $opts = [
        "l:" => "logid:"
    ];

    private $config;

    protected $arguments;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->arguments = getopt(implode("", self::getShortOpts()), self::getLongOpts());
    }

    public function getFile() : File
    {
        return $this->getLogFileOrCreateIfNonExistent();
    }

    protected abstract function getLogFileName() : string;

    protected function getLogId() : int
    {
        if (isset($this->arguments['l'])) return ((int)$this->arguments['l']) + 1;

        if (isset($this->arguments['logid'])) return ((int)$this->arguments['logid']) + 1;

        return getmypid();
    }

    protected static function getShortOpts() : array
    {
        return array_keys(static::$opts);
    }

    protected static function getLongOpts() : array
    {
        return array_values(static::$opts);
    }

    protected function getLogFileOrCreateIfNonExistent()
    {
        $path = $this->config->path("logging.file.path");

        $file = sprintf("%s/%s", $path, $this->getLogFileName());

        if (! file_exists($file)) {
            mkdir($path);
            touch($file);
        }

        return new File($file);
    }
}