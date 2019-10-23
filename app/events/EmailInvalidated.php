<?php

namespace App\Events;

use App\StateMachines\InvalidEmail;

class EmailInvalidated extends Event implements EventContract
{
    use EmailEvent;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return [
            'email' => $this->data['email'],
            'status' => new InvalidEmail(),
            'user_id' => $this->data['user_id']
        ];
    }
}