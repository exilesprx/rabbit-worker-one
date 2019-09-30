<?php

namespace App\Loggers;

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;

class LogStashLogger
{
    private $logger;

    public function __construct(Logger $logger, SocketHandler $handler, LogstashFormatter $formatter)
    {
        $handler->setFormatter($formatter);

        $logger->pushHandler($handler);

        $this->logger = $logger;
    }

    public function info(string $message, array $payload)
    {
        $this->logger->info($message, $payload);

        $this->logger->close();
    }

    public function critical(string $message)
    {
        $this->logger->critical($message);

        $this->logger->close();
    }

    public function debug(string $message)
    {
        $this->logger->debug($message);

        $this->logger->close();
    }

    public function alert(string $message)
    {
        $this->logger->alert($message);

        $this->logger->close();
    }
}