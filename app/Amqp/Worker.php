<?php

namespace App\Amqp;

use App\Entities\AmqpWorker;
use App\Loggers\AmqpLogger;
use App\Loggers\LogStashLogger;
use App\Queue\Queue;
use App\ValueObjects\AmqpDeliveryTag;
use App\ValueObjects\BeanstalkTube;
use Closure;
use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Worker
{
    private $connection;

    private $queue;

    private $logstash;

    private $logger;

    public function __construct(AMQPStreamConnection $connection, Queue $queue, LogStashLogger $logstash, AmqpLogger $logger)
    {
        $this->connection = $connection;

        $this->queue = $queue;

        $this->logstash = $logstash;

        $this->logger = $logger->getFile();
    }

    public function work(AmqpWorker $worker, BeanstalkTube $tube)
    {
        $channel = $this->connection->channel();

        $this->initializeQueueAndExchange($channel, $worker);

        $this->queue->connect($tube);

        $this->logger->info(" [*] Waiting for messages. To exit press CTRL+C");

        $channel->basic_consume($worker->getQueueName(), $worker->getConsumerTag(), false, false, false, false, $this->makeClosure());

        while (count($channel->callbacks)) {
            try {
                $channel->wait();
            } catch (Exception $exception)
            {
                $this->logger->emergency(sprintf("An error occurred: %s", $exception->getMessage()));
            }
        }
    }

    public function close()
    {
        try {
            $this->connection->close();
        } catch (Exception $exception) {
            $this->logger->critical(sprintf("An error occurred when attempting to close the connection: %s", $exception->getMessage()));
        } finally {
            $this->queue->disconnect();
        }
    }

    private function initializeQueueAndExchange(AMQPChannel $channel, AmqpWorker $worker)
    {
        $channel->queue_declare($worker->getQueueName(), false, true, false, false);

        $channel->exchange_declare($worker->getExchangeName(), $worker->getExchangeType());

        $channel->queue_bind($worker->getQueueName(), $worker->getExchangeName(), $worker->getQueueName());
    }

    private function makeClosure() : Closure
    {
        return function(AMQPMessage $message) {

            $this->queue->putLowPriorityJob($message);

            $channel = self::getChannelFromMessage($message);

            $deliveryTag = self::getDeliveryTagFromMessage($message);

            $channel->basic_ack($deliveryTag->getValue());

            $this->logstash->info("Consumed-Event", self::parseMessage($message));
        };
    }

    private static function getChannelFromMessage(AMQPMessage $message) : AMQPChannel
    {
        return $message->delivery_info['channel'];
    }

    private static function getDeliveryTagFromMessage(AMQPMessage $message) : AmqpDeliveryTag
    {
        return new AmqpDeliveryTag($message->delivery_info['delivery_tag']);
    }

    private static function parseMessage(AMQPMessage $message) : array
    {
        return json_decode($message->body, true);
    }
}