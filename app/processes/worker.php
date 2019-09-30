<?php

namespace App\Processes;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Providers\ServiceProvider;
use App\Queue\Queue;
use App\ValueObjects\BeanstalkTube;
use Phalcon\Di\FactoryDefault;

$di = new FactoryDefault();

$provider = new ServiceProvider();

$provider->register($di);

/** @var Queue $queue */
$queue = $di->getShared('queue');

// TODO: update this to pull the queue name from the args passed
$queue->connect(new BeanstalkTube('phalcon'));

//$queue->cleanupBuried();
//$queue->cleanUpQueue();

$queue->workReserved();

$queue->disconnect();