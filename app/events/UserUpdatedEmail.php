<?php

namespace App\Events;

class UserUpdatedEmail extends UserEvent
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData() : array
    {
        return $this->data;
    }
}