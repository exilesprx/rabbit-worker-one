<?php

namespace App\Listeners;

use App\events\UserUpdatedEmail;
use App\models\User;
use App\Store\Email;
use Phalcon\Events\Event;
use Phalcon\Logger\Adapter\File as Logger;
use Ramsey\Uuid\Uuid;

class UserTaskListener extends Listener
{
    protected $logger;

    protected static $events = [
        UserUpdatedEmail::class
    ];

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onUserUpdatedEmail(Event $event, $task)
    {
        $data = $event->getData();

        $id = $data['payload']['user_id'];
        $email = $data['payload']['email'];
        $version = $data['payload']['version'];

        $user = User::findFirst(
            [
                'id' => $id
            ]
        );

        $user->update(
            [
                'email' => $email,
                'version' => $version
            ]
        );

        $payload = $event->getData();

        $this->logger->info(' [x] Received ' . json_encode($payload));

        $email = $this->initializeEmailStore($payload);

        $email->save();

        $this->logger->info( " [x] Done");
    }

    private function initializeEmailStore(array $data) : Email
    {
        return Email::with(
            Uuid::fromString($data['uuid']),
            $data['payload']['user_id'],
            $data['payload']['version'],
            $data['payload']['email']
        );
    }
}