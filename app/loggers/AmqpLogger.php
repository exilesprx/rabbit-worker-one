<?php

namespace App\Loggers;

class AmqpLogger extends ProcessLogger
{
    protected function getLogFileName(): string
    {
        return sprintf("%s-%d.log", "amqp-consumer", $this->getLogId());
    }
}