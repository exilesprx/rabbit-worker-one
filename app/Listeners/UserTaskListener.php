<?php

namespace App\Listeners;

use App\Commands\UserUpdatedEmail;
use App\Repositories\UserRepository;
use App\Tasks\TaskConductor;
use Phalcon\Events\Event;
use Phalcon\Logger\Adapter\File as Logger;

class UserTaskListener extends Listener
{
    protected $logger;

    protected $repository;

    protected $conductor;

    protected static $events = [
        UserUpdatedEmail::class
    ];

    public function __construct(Logger $logger, UserRepository $repository, TaskConductor $conductor)
    {
        $this->logger = $logger;

        $this->repository = $repository;

        $this->conductor = $conductor;
    }

    /**
     * @param Event $event
     * @param $component
     * @param UserUpdatedEmail $command
     * @throws \App\Exceptions\InvalidUpdateException
     * @throws \App\Exceptions\OutOfOrderException
     */
    public function onUserUpdatedEmail(Event $event)
    {
        /** @var UserUpdatedEmail $command */
        $command = $event->getData();

        $user = $this->repository->findUserById($command->getId());

        $user->updateUserEmail($command);

        $user->recordEvents($this->conductor);

        $this->repository->updateEmail($user);
    }
}