<?php

namespace App\AggregateRoots;

use App\Commands\UserUpdatedEmail;
use App\Entities\EmailValidation;
use App\Events\UserEmailUpdated;
use App\Exceptions\InvalidUpdateException;
use App\Exceptions\OutOfOrderException;
use App\StateMachines\EmailValidationState;
use App\Tasks\Task;
use App\Tasks\TaskCollection;
use App\Tasks\TaskConductor;

class User
{
    private $id;

    private $email;

    private $version;

    /** @var EmailValidation */
    private $emailValidation;

    protected $tasks;

    public function __construct(int $id, string $email, int $version, EmailValidation $emailValidation)
    {
        $this->id = $id;

        $this->email = $email;

        $this->version = $version;

        $this->emailValidation = $emailValidation;

        $this->tasks = new TaskCollection();
    }

    public function updateUserEmail(UserUpdatedEmail $command)
    {
        if ($command->getId() != $this->id) {
            throw InvalidUpdateException::invalidEntityUpdate();
        }

        if (!$this->isNextVersion($command->getVersion())) {
            throw OutOfOrderException::job($this->version, $command->getVersion());
        }

        // We've satisfied all business logic (not much here atm) so update the AR and add our events.

        $this->version = $command->getVersion();
        $this->email = $command->getEmail();

        $this->emailValidation->updateStatus($command->getEmail());

        $this->tasks->addTask(
            new Task(
                UserEmailUpdated::getUblName(),
                $command->getData()
            )
        );
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function getVersion() : int
    {
        return $this->version;
    }

    public function isEmailValid() : bool
    {
        return $this->emailValidation->isValid();
    }

    public function getEmailStatus() : EmailValidationState
    {
        return $this->emailValidation->getStatus();
    }

    public function recordEvents(TaskConductor $conductor)
    {
        $conductor->executeTasks($this->tasks->flush());

        $this->emailValidation->recordEvents($conductor);
    }

    public function hasTasks() : bool
    {
        return $this->tasks->hasTasks()
            || $this->emailValidation->hasTasks();
    }

    private function isNextVersion(int $nextVersion) : bool
    {
        return ($nextVersion === 1 && $this->version === 1)
            || $this->version + 1 === $nextVersion;
    }
}