<?php

namespace App\Entities;

use Phalcon\Queue\Beanstalk\Job;

class QueueJobBody implements Arrayable
{
    private $id;

    private $payload;

    protected function __construct(string $id, array $payload)
    {
        $this->id = $id;

        $this->payload = $payload;
    }

    public static function from(Job $job) : self
    {
        $id = $job->getId();

        $payload = json_decode($job->getBody(), true);

        return new self($id, $payload);
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getType() : string
    {
        return $this->payload['name'];
    }

    public function getVersion() : int
    {
        return $this->payload['payload']['version'];
    }

    public function isSameType(self $queueJobBody) : bool
    {
        return $queueJobBody instanceof self
            && $this->getType() === $queueJobBody->getType();
    }

    public function toArray(): array
    {
        return $this->payload;
    }
}