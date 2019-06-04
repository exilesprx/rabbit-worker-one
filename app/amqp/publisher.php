<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('worker_one', 'direct');

$channel->queue_bind('task_queue.two', 'worker_one', 'task_queue.two');

$multiplier = 1;

while(true) {
    for ($i = 0; $i < 10000; $i++) {

        $data = [
            'uuid' => '',
            'name' => 'basic.task',
            'payload' => [
                'value' => $multiplier * $i
            ]
        ];

        $msg = new AMQPMessage(
            json_encode($data),
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );

        $channel->basic_publish($msg, 'worker_one', 'task_queue.two');

        usleep(rand(1, 300));
    }

    $multiplier++;

    sleep(rand(10, 60));
}

$channel->close();
$connection->close();