<?php

namespace App\Exceptions;

use Phalcon\Exception;

class InvalidUpdateException extends Exception
{
    public function __construct()
    {
        $message = "Attempting to update the wrong entity instance.";

        parent::__construct($message, 0, null);
    }

    public static function invalidEntityUpdate() : self
    {
        return new self();
    }
}