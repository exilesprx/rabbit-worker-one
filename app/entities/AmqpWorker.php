<?php

namespace App\Entities;

use App\ValueObjects\AmqpConsumerTag;
use App\ValueObjects\AmqpExchange;
use App\ValueObjects\AmqpQueue;
use App\ValueObjects\AmqpWorkerType;

class AmqpWorker
{
    private $queue;

    private $exchange;

    private $type;

    private $consumerTag;

    public function __construct(AmqpQueue $queue, AmqpExchange $exchange, AmqpWorkerType $workerType, AmqpConsumerTag $consumerTag)
    {
        $this->queue = $queue;

        $this->exchange = $exchange;

        $this->type = $workerType;

        $this->consumerTag = $consumerTag;
    }

    public function getQueueName() : string
    {
        return (string)$this->queue;
    }

    public function getExchangeName() : string
    {
        return (string)$this->exchange;
    }

    public function getExchangeType() : string
    {
        return (string)$this->type;
    }

    public function getConsumerTag() : string
    {
        return (string)$this->consumerTag;
    }

    public function isEqual(AmqpWorker $worker) : bool
    {
        return $worker instanceof self
            && $worker->queue->getValue() === $this->queue->getValue()
            && $worker->exchange->getValue() === $this->exchange->getValue()
            && $worker->type->getValue() === $this->type->getValue();
    }
}