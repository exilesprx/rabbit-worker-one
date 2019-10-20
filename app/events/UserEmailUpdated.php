<?php

namespace App\Events;

class UserEmailUpdated extends UserEvent
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData() : array
    {
        return [
            'id' => $this->data['payload']['user_id'],
            'email' => $this->data['payload']['email'],
            'version' => $this->data['payload']['email']
        ];
    }
}