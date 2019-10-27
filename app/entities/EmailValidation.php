<?php

namespace App\Entities;

use App\Events\EmailInvalidated;
use App\Events\EmailValidated;
use App\StateMachines\EmailValidationState;
use App\StateMachines\InvalidEmail;
use App\StateMachines\ValidEmail;
use App\Tasks\Task;
use App\Tasks\TaskCollection;

class EmailValidation extends Entity implements EventableEntityContract
{
    use ProducesEvents, RecordsEvents;

    private $id;

    private $userId;

    private $status;

    public function __construct(int $id, int $userId, EmailValidationState $status)
    {
        $this->id = $id;

        $this->userId = $userId;

        $this->status = $status;

        $this->events = new TaskCollection();
    }

    public function updateStatus(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $this->transitionStatusTo(new InvalidEmail());

            $this->recordTask(
                new Task(
                    EmailInvalidated::getUblName(),
                    [
                        'email' => $email,
                        'user_id' => $this->userId
                    ]
                )
            );

            return;
        }

        $this->transitionStatusTo(new ValidEmail());

        $this->recordTask(
            new Task(
                EmailValidated::getUblName(),
                [
                    'email' => $email,
                    'user_id' => $this->userId
                ]
            )
        );

        return;
    }

    public function isValid() : bool
    {
        return $this->status instanceof ValidEmail;
    }

    public function getStatus(): EmailValidationState
    {
        return $this->status;
    }

    private function transitionStatusTo(EmailValidationState $status)
    {
        $this->status = $this->status->transitionTo($status);
    }
}