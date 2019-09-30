<?php

namespace App\Processes;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Amqp\Worker;
use App\Providers\ServiceProvider;
use App\ValueObjects\AmqpConsumerTag;
use App\ValueObjects\AmqpDirectExchange;
use App\ValueObjects\AmqpExchange;
use App\ValueObjects\AmqpQueue;
use App\Entities\AmqpWorker;
use App\ValueObjects\BeanstalkTube;
use Phalcon\Di\FactoryDefault;

$di = new FactoryDefault();

$provider = new ServiceProvider();

$provider->register($di);

/** @var Worker $amqp */
$amqp = $di->getShared('amqp-worker');

$worker = new AmqpWorker(
    new AmqpQueue('task_queue.one'),
    new AmqpExchange('worker_one'),
    new AmqpDirectExchange(),
    new AmqpConsumerTag()
);

// TODO: Get queue name from args
$amqp->work($worker, new BeanstalkTube('phalcon'));

$amqp->close();