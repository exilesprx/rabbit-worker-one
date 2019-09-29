<?php

namespace App\Amqp;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Valueobjects\LowPriorityJob;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;
use Phalcon\Di\FactoryDefault;
use App\Providers\ServiceProvider;
use Phalcon\Queue\Beanstalk;

$di = new FactoryDefault();
$provider = new ServiceProvider();

$provider->register($di);

$logger = $di->getShared('logger');

$log = new Logger("phalcon-one-consumer");

$handler = new SocketHandler("logstash:5055");

$formatter = new LogstashFormatter("phalcon");

$handler->setFormatter($formatter);

$log->pushHandler($handler);

$connection = $di->getShared('amqp');

$channel = $connection->channel();

$queue = new Beanstalk(
    [
        'host' => 'beanstalkd',
        'port' =>11300
    ]
);

$queue->connect();

$channel->queue_declare('task_queue.one', false, true, false, false);

$channel->exchange_declare('worker_one', 'direct');

$channel->queue_bind('task_queue.one', 'worker_one', 'task_queue.one');

$logger->info(" [*] Waiting for messages. To exit press CTRL+C");

$callback = function ($msg) use($di, $queue, $log) {

    $queue->choose("phalcon");

    $payload = json_decode($msg->body, true);

    $queue->put(
        $msg->body,
        [
            'priority' => LowPriorityJob::getValue()
        ]
    );

    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

    $log->info("Consumed-Event", $payload);

    $log->close();
};

$channel->basic_consume('task_queue.one', '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();