<?php

namespace App\Listeners;

use App\AggregateRoots\User;
use App\Events\UserEmailUpdated;
use App\Events\UserUpdatedEmail;
use App\Store\Email;
use App\Tasks\TaskConductor;
use Phalcon\Events\Event;
use Phalcon\Logger\Adapter\File as Logger;
use Ramsey\Uuid\Uuid;

class UserTaskListener extends Listener
{
    protected $logger;

    protected $repository;

    protected $conductor;

    protected static $events = [
        UserUpdatedEmail::class,
        UserEmailUpdated::class
    ];

    public function __construct(Logger $logger, TaskConductor $conductor)
    {
        $this->logger = $logger;

        $this->repository = User::getRepository();

        $this->conductor = $conductor;
    }

    /**
     * @param Event $event
     * @param $task
     * @throws \App\Exceptions\OutOfOrderException
     */
    public function onUserUpdatedEmail(Event $event, $task)
    {
        // TODO: This should be moved to the TaskCollection class during the flush
        $this->insertEvent($event->getData());

        $userId = $event->getData()['id'];

        $user = $this->repository->findUserById($userId);

        $user->updateUserEmail($event);

        $user->recordEvents($this->conductor);

        $user->save();
    }

    /**
     * @param Event $event
     */
    public function onUserEmailUpdated(Event $event)
    {
        // TODO: Move to reactor
    }

    private function insertEvent(array $data)
    {
        $this->logger->alert(sprintf("Inserting email with version %d", $data['payload']['version']));

        Email::with(
            Uuid::fromString($data['uuid']),
            $data['payload']['user_id'],
            $data['payload']['version'],
            $data['payload']['email']
        )->save();
    }
}