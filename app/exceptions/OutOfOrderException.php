<?php

namespace App\Exceptions;

use Phalcon\Exception;

class OutOfOrderException extends Exception
{
    private $currentVersion;

    private $nextVersion;

    public function __construct(string $message, int $currentVersion, int $nextVersion)
    {
        parent::__construct($message, 0, null);

        $this->currentVersion = $currentVersion;

        $this->nextVersion = $nextVersion;
    }

    public static function job(int $has, int $received) : self
    {
        $message = sprintf("The current job is out of order. Has: %d Wants: %d Received: %d", $has, $has + 1, $received);

        return new self($message, $has, $received);
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
        return $this->nextVersion;
    }

    public function getDifference() : int
    {
        return $this->nextVersion - $this->currentVersion;
    }
}