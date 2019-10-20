<?php

namespace App\AggregateRoots;

use App\Entities\EmailValidation;
use App\Events\UserEmailUpdated;
use App\Events\UserUpdatedEmail;
use App\Exceptions\InvalidUpdateException;
use App\Exceptions\OutOfOrderException;
use App\Repositories\UserApplicationLayerContract;
use App\Repositories\UserRepository;
use App\Repositories\UserService;
use App\Tasks\Task;
use App\Tasks\TaskCollection;
use App\Tasks\TaskConductor;
use Phalcon\Di;

class User implements \App\AggregateRoots\UserApplicationLayerContract
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

    public static function getRepository() : UserApplicationLayerContract
    {
        return Di::getDefault()->get(UserService::class);
    }

    public function save()
    {
        /** @var UserRepository $repo */
        $repo = Di::getDefault()->get(UserRepository::class);

        $repo->updateEmail($this);

        $this->emailValidation->save();
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

        $this->emailValidation->updateStatus($email);

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

    public function getEmailStatus() : string
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