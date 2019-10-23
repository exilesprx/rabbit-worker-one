<?php

namespace App\Reactors;

use App\Events\UserEmailUpdated;
use App\Listeners\Listener;
use App\Listeners\ListenerContract;
use App\Store\Email;
use Phalcon\Events\Event;
use Phalcon\Logger\Adapter\File as Logger;

class UserReactor extends Listener implements ListenerContract, Reactor
{
    private $logger;

    protected static $events = [
        UserEmailUpdated::class
    ];

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onUserEmailUpdated(Event $event)
    {
        /** @var UserEmailUpdated $event */
        $event = $event->getData();

        $this->logger->alert(sprintf("Inserting email with version %d", $event->getVersion()));

        Email::with(
            $event->getUuid(),
            $event->getId(),
            $event->getVersion(),
            $event->getEmail()
        )->save();
    }
}