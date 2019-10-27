<?php

namespace App\Commands;

use App\Events\EventContract;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UserUpdatedEmail extends UserCommand implements EventContract
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getUuid() : UuidInterface
    {
        return Uuid::fromString($this->data['uuid']);
    }

    public function getId() : int
    {
        return $this->data['payload']['user_id'];
    }

    public function getEmail() : string
    {
        return $this->data['payload']['email'];
    }

    public function getVersion() : int
    {
        return $this->data['payload']['version'];
    }

    public function getData() : array
    {
        return [
            'id' => $this->getId(),
            'uuid' => $this->getUuid(),
            'email' => $this->getEmail(),
            'version' => $this->getVersion()
        ];
    }
}