<?php

namespace App\Loggers;

class QueueLogger extends ProcessLogger
{
    protected function getLogFileName(): string
    {
        return sprintf("%s-%d.log", "queue-worker", $this->getLogId());
    }
}