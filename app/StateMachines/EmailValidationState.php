<?php

namespace App\StateMachines;

interface EmailValidationState
{
    public function transitionTo(EmailValidationState $next) : EmailValidationState;

    public static function equals(string $status) : bool;
}