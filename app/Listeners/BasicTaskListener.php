<?php


namespace App\Listeners;


use App\Store\Email;
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
        $payload = $event->getData();

        $this->logger->info(' [x] Received ' . json_encode($payload));

        $email = $this->initializeEmail($payload);

        $this->logger->info($email->readAttribute('user_id'));

        $email->save();

        $this->logger->info( " [x] Done");
    }

    private function initializeEmail(array $payload) : Email
    {
        return Email::fromArray(
            $payload['user_id'],
            $payload['version'],
            $payload['email']
        );
    }
}