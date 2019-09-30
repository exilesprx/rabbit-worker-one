<?php

namespace App\Exceptions;

use Phalcon\Exception;

class OutOfOrderException extends Exception
{
    private $currentVersion;

    public function __construct(string $message, int $currentVersion)
    {
        parent::__construct($message, 0, null);

        $this->currentVersion = $currentVersion;
    }

    public static function job(int $has, int $received) : self
    {
        $message = sprintf("The current job is out of order. Has: %d -- Wants: %d -- Received: %d", $has, $has + 1, $received);

        return new self($message, $has);
    }

    /**
     * @return int
     */
    public function getCurrentVersion(): int
    {
        return $this->currentVersion;
    }

    /**
     * @return int
     */
    public function getNextVersion(): int
    {
        return $this->currentVersion + 1;
    }

    public function getDifference() : int
    {
        return $this->getNextVersion() - $this->currentVersion;
    }
}