<?php

namespace App\StateMachines;

use Phalcon\Exception;

class InvalidNextStateException extends Exception
{
    public function __construct()
    {
        $message = "Attempting to move to an incorrect state";

        parent::__construct($message, 0, null);
    }

    public static function invalidState()
    {
        return new self();
    }
}