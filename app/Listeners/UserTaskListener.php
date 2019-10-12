<?php

namespace App\Listeners;

use App\Events\UserUpdatedEmail;
use App\Exceptions\OutOfOrderException;
use App\Models\User;
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

        /** @var User $user */
        $user = User::findFirst(
            [
                'id' => $id
            ]
        );

        if (!$this->isNextVersion($user, $version)) {
            throw OutOfOrderException::job($user->getVersion(), $version);
        }
        $this->logger->critical(sprintf("Updated user version to %d", $version));
        $user->update(
            [
                'email' => $email,
                'version' => $version
            ]
        );

        $payload = $event->getData();

        $email = $this->initializeEmailStore($payload);

        $email->save();
    }

    private function initializeEmailStore(array $data) : Email
    {
        $this->logger->alert(sprintf("Inserting email with version %d", $data['payload']['version']));

        return Email::with(
            Uuid::fromString($data['uuid']),
            $data['payload']['user_id'],
            $data['payload']['version'],
            $data['payload']['email']
        );
    }

    private function isNextVersion(User $user, int $nextVersion) : bool
    {
        return ($nextVersion === 1 && $user->getVersion() === 1)
            || $user->getVersion() + 1 === $nextVersion;
    }
}