<?php

namespace App\Processes;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Amqp\Worker;
use App\Helpers\AmqpProcessHelper;
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

$process = new AmqpProcessHelper();

$worker = new AmqpWorker(
    new AmqpQueue($process->getQueue()),
    new AmqpExchange($process->getExchange()),
    new AmqpDirectExchange(),
    new AmqpConsumerTag()
);

$amqp->work($worker, new BeanstalkTube($process->getTube()));

$amqp->close();