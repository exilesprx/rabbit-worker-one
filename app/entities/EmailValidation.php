<?php

namespace App\Entities;

use App\Repositories\EmailValidationRepository;
use App\Tasks\Task;
use App\Tasks\TaskCollection;
use App\Tasks\TaskConductor;
use Phalcon\Di;

class EmailValidation
{
    private $id;

    private $userId;

    private $status;

    private $tasks;

    public function __construct(int $id, int $userId, string $status)
    {
        $this->id = $id;

        $this->userId = $userId;

        $this->status = $status;

        $this->tasks = new TaskCollection();
    }

    public static function getRepository() : EmailValidationRepository
    {
        return Di::getDefault()->get(EmailValidationRepository::class);
    }

    public function save()
    {
        /** @var EmailValidationRepository $repo */
        $repo = self::getRepository();

        $repo->updateStatus($this);
    }

    public function updateStatus(string $email)
    {
        // TODO: Update the status to use a state machine.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->status = "invalid";

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

        $this->status = "valid";

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
        return $this->status == "valid";
    }

    public function getStatus(): string
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
}