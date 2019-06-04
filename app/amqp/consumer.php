<?php

namespace App\Amqp;

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Phalcon\Logger\Adapter\File;
use Phalcon\Di\FactoryDefault;
use App\Providers\ServiceProvider;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$logger = new File('log.log');
$di = new FactoryDefault();
$provider = new ServiceProvider();

$provider->register($di);

$channel = $connection->channel();

$channel->queue_declare('task_queue.one', false, true, false, false);

$channel->exchange_declare('worker_one', 'direct');

$channel->queue_bind('task_queue.one', 'worker_one', 'task_queue.one');

$logger->info(" [*] Waiting for messages. To exit press CTRL+C");

$callback = function ($msg) use($logger, $di) {
    $payload = json_decode($msg->body, true);

    $task = $di->get($payload['name']);

    $task->execute($payload['payload']);

    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_consume('task_queue.one', '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();