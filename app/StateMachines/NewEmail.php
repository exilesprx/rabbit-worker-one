<?php

namespace App\StateMachines;

use App\ValueObjects\Stringable;

class NewEmail implements EmailValidationState, Stringable
{
    use EmailValidationTrait;

    protected static $status = "new";

    public function transitionTo(EmailValidationState $next) : EmailValidationState
    {
        if ($next instanceof ValidEmail || $next instanceof InvalidEmail) {
            return $next;
        }

        throw InvalidNextStateException::invalidState();
    }
}