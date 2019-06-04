<?php


namespace App\Listeners;


use Phalcon\Events\Event;
use Phalcon\Logger\Adapter\File as Logger;
use Phalcon\Mvc\User\Plugin;

class BasicTaskListener extends Plugin
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Event $event, $task)
    {
        $this->logger->info(' [x] Received ' . json_encode($event->getData()));

        $this->logger->info( " [x] Done");
    }
}