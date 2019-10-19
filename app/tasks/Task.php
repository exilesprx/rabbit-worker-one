<?php

namespace App\Tasks;

class Task
{
    private $name;

    private $payload;

    public function __construct(string $name, array $payload)
    {
        $this->name = $name;

        $this->payload = $payload;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getPayload() : array
    {
        return $this->payload;
    }

    public function toArray() : array
    {
        return array_merge(
            [
                'name' => $this->name
            ],
            $this->payload
        );
    }
}