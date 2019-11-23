<?php

namespace App\Entities;

use App\ValueObjects\AmqpDeliveryTag;
use App\ValueObjects\Stringable;
use PhpAmqpLib\Channel\AMQPChannel;
use Ramsey\Uuid\Uuid;

class AmqpMessage implements Arrayable, Stringable
{
    protected $id;

    protected $body;

    protected $channel;

    protected $deliveryTag;

    public function __construct(string $data, AmqpChannel $channel, AmqpDeliveryTag $deliveryTag)
    {
        $this->body = json_decode($data, true);

        $this->id = $this->body['uuid'];

        $this->channel = $channel;

        $this->deliveryTag = $deliveryTag;
    }

    /**
     * @return Uuid
     */
    public function getId() : Uuid
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getBody() : array
    {
        return $this->body;
    }

    /**
     * @return AmqpChannel
     */
    public function getChannel(): AmqpChannel
    {
        return $this->channel;
    }

    /**
     * @return AmqpDeliveryTag
     */
    public function getDeliveryTag(): AmqpDeliveryTag
    {
        return $this->deliveryTag;
    }

    /**
     * @return int
     */
    public function getVersion() : int
    {
        return $this->body['payload']['version'];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body
        ];
    }

    public function __toString(): string
    {
        return json_decode($this->body);
    }
}