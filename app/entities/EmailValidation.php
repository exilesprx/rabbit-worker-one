<?php

namespace App\Entities;

use App\StateMachines\EmailValidationState;
use App\StateMachines\InvalidEmail;
use App\StateMachines\ValidEmail;
use App\Tasks\Task;
use App\Tasks\TaskCollection;
use App\Tasks\TaskConductor;

class EmailValidation
{
    private $id;

    private $userId;

    private $status;

    private $tasks;

    public function __construct(int $id, int $userId, EmailValidationState $status)
    {
        $this->id = $id;

        $this->userId = $userId;

        $this->status = $status;

        $this->tasks = new TaskCollection();
    }

    public function updateStatus(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->transitionStatusTo(new InvalidEmail());

            $this->tasks->addTask(
                new Task(
                    'email.invalid.validation',
                    [
                        'email' => $email,
                        'user_id' => $this->userId
                    ]
                )
            );

            return;
        }

        $this->transitionStatusTo(new ValidEmail());

        $this->tasks->addTask(
            new Task(
                'email.valid.validation',
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

    public function hasTasks() : bool
    {
        return $this->tasks->hasTasks();
    }

    public function recordEvents(TaskConductor $conductor)
    {
        $conductor->executeTasks($this->tasks->flush());
    }

    private function transitionStatusTo(EmailValidationState $status)
    {
        $this->status = $this->status->transitionTo($status);
    }
}