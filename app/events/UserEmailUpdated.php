<?php

namespace App\Events;

use Ramsey\Uuid\UuidInterface;

class UserEmailUpdated extends Event implements EventContract
{
    use UserEvent;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getUuid() : UuidInterface
    {
        return $this->data['uuid'];
    }

    public function getId() : int
    {
        return $this->data['id'];
    }

    public function getEmail() : string
    {
        return $this->data['email'];
    }

    public function getVersion() : int
    {
        return $this->data['version'];
    }

    public function getData() : array
    {
        return [
            'id' => $this->data['id'],
            'uuid' => $this->data['uuid'],
            'email' => $this->data['email'],
            'version' => $this->data['version']
        ];
    }
}