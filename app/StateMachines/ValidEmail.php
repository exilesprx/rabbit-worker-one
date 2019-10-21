<?php

namespace App\StateMachines;

use App\ValueObjects\Stringable;

class ValidEmail implements EmailValidationState, Stringable
{
    use EmailValidationTrait;

    protected static $status = "valid";

    public function transitionTo(EmailValidationState $next) : EmailValidationState
    {
        if ($next instanceof ValidEmail || $next instanceof InvalidEmail) {
            return $next;
        }

        throw InvalidNextStateException::invalidState();
    }
}