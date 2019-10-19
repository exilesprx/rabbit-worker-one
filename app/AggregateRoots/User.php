<?php

namespace App\AggregateRoots;

use App\Events\UserEmailUpdated;
use App\Events\UserUpdatedEmail;
use App\Exceptions\InvalidUpdateException;
use App\Exceptions\OutOfOrderException;
use App\Tasks\Task;
use App\Tasks\TaskCollection;

class User
{
    private $id;

    private $email;

    private $version;

    private $tasks;

    public function __construct(int $id, string $email, int $version)
    {
        $this->id = $id;

        $this->email = $email;

        $this->version = $version;

        // TODO: Will probably have to make the TaskCollection a singleton so other entities can push tasks to the same collection.
        $this->tasks = new TaskCollection();
    }

    public static function fromArray(array $data) : self
    {
        return new self($data['id'], $data['version'], $data['version']);
    }

    public function updateUserEmail(UserUpdatedEmail $command)
    {
        list($id, $email, $version) = array_values($command->getData());

        if ($id != $this->id) {
            throw InvalidUpdateException::invalidEntityUpdate();
        }

        if (!$this->isNextVersion($version)) {
            throw OutOfOrderException::job($this->version, $version);
        }

        // We've satisfied all business logic (not much here atm) so update the AR and add our events.

        $this->version = $version;

        $this->email = $email;

        $this->tasks->addTask(
            new Task(
                UserEmailUpdated::getUblName() ,
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

    public function getTasks() : TaskCollection
    {
        return $this->tasks;
    }

    private function isNextVersion(int $nextVersion) : bool
    {
        return ($nextVersion === 1 && $this->version === 1)
            || $this->version + 1 === $nextVersion;
    }
}