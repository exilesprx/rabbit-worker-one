<?php

namespace App\Reactors;

use App\Events\EmailInvalidated;
use App\Events\EmailValidated;
use App\Listeners\Listener;
use App\Listeners\ListenerContract;
use Phalcon\Events\Event;
use Phalcon\Logger\Adapter\File as Logger;

class EmailValidationReactor extends Listener implements ListenerContract, Reactor
{
    protected $logger;

    protected static $events = [
        EmailValidated::class,
        EmailInvalidated::class
    ];

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onEmailValidated(Event $event)
    {
        /** @var EmailValidated $domainEvent */
        $domainEvent = $event->getData();

        $this->logger->debug("Email validated", $domainEvent->getData());
    }

    public function onEmailInvalidated(Event $event)
    {
        /** @var EmailInvalidated $domainEvent */
        $domainEvent = $event->getData();

        $this->logger->debug("Email invalidated", $domainEvent->getData());
    }
}