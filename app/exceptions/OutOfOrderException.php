<?php

namespace App\Exceptions;

use Phalcon\Exception;

class OutOfOrderException extends Exception
{
    private $currentVersion;

    private $receivedVersion;

    public function __construct(string $message, int $currentVersion, int $receivedVersion)
    {
        parent::__construct($message, 0, null);

        $this->currentVersion = $currentVersion;

        $this->receivedVersion = $receivedVersion;
    }

    public static function job(int $has, int $received) : self
    {
        $message = sprintf("The current job is out of order. Has: %d -- Wants: %d -- Received: %d", $has, $has + 1, $received);

        return new self($message, $has, $received);
    }

    public function getDifference() : int
    {
        return abs($this->currentVersion - $this->receivedVersion);
    }
}