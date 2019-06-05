<?php


namespace App\Listeners;


use App\Store\Email;
use Phalcon\Events\Event;
use Phalcon\Logger\Adapter\File as Logger;
use Phalcon\Mvc\User\Plugin;
use Ramsey\Uuid\Uuid;

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

        $email->save();

        $this->logger->info( " [x] Done");
    }

    private function initializeEmail(array $data) : Email
    {
        return Email::with(
            Uuid::fromString($data['uuid']),
            $data['payload']['user_id'],
            $data['payload']['version'],
            $data['payload']['email']
        );
    }
}