<?php

use App\StateMachines\EmailValidationState;
use App\StateMachines\InvalidEmail;
use App\StateMachines\InvalidNextStateException;
use App\StateMachines\NewEmail;
use App\StateMachines\ValidEmail;
use Codeception\Test\Unit;

class EmailValidationStateMachineTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /** @var EmailValidationState */
    protected $status;
    
    protected function _before()
    {
        $this->status = new NewEmail();
    }

    // tests
    public function testNewEmailStatusCannotTransitionToNewEmailStatus()
    {
        $this->tester->expectThrowable(InvalidNextStateException::class, function() {
            $this->status->transitionTo(new NewEmail());
        });
    }

    public function testNewEmailStatusCanTransitionToValidEmailStatus()
    {
        $this->tester->expect($this->status->transitionTo(new ValidEmail()));
    }

    public function testNewEmailStatusCanTransitionToInvalidEmailStatus()
    {
        $this->tester->expect($this->status->transitionTo(new InvalidEmail()));
    }

    public function testInvalidEmailStatusCanTransitionToInvalidEmailStatus()
    {
        $this->status->transitionTo(new InvalidEmail());

        $this->tester->expect(new InvalidEmail());
    }

    public function testInvalidEmailStatusCanTransitionToValidEmailStatus()
    {
        $this->status->transitionTo(new InvalidEmail());

        $this->tester->expect($this->status->transitionTo(new ValidEmail()));
    }

    public function testInvalidEmailStatusCannotTransitionToNewEmailStatus()
    {
        $this->status->transitionTo(New InvalidEmail());

        $this->tester->expectThrowable(InvalidNextStateException::class, function() {
            $this->status->transitionTo(new NewEmail());
        });
    }

    public function testValidEmailStatusCanTransitionToValidEmailStatus()
    {
        $this->status->transitionTo(new ValidEmail());

        $this->tester->expect($this->status->transitionTo(new ValidEmail()));
    }

    public function testValidEmailStatusCanTransitionToInvalidEmailStatus()
    {
        $this->status->transitionTo(new ValidEmail());

        $this->tester->expect($this->status->transitionTo(new InvalidEmail()));
    }

    public function testValidEmailStatusCannotTransitionToNewEmailStatus()
    {
        $this->status->transitionTo(new ValidEmail());

        $this->tester->expectThrowable(InvalidNextStateException::class, function() {
            $this->status->transitionTo(new NewEmail());
        });
    }
}