<?php

namespace App\Processes;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Amqp\Worker;
use App\Helpers\AmqpProcess;
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
$amqp = $di->getShared(Worker::class);

$process = new AmqpProcess();

$worker = new AmqpWorker(
    new AmqpQueue($process->getQueue()),
    new AmqpExchange($process->getExchange()),
    new AmqpDirectExchange(),
    new AmqpConsumerTag()
);

$queue = new BeanstalkTube($process->getTube());

$amqp->work($worker, $queue);

$amqp->close();