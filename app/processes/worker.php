<?php

namespace App\Processes;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Helpers\QueueProcessHelper;
use App\Providers\ServiceProvider;
use App\Queue\Queue;
use App\ValueObjects\BeanstalkTube;
use Phalcon\Di\FactoryDefault;

$di = new FactoryDefault();

$provider = new ServiceProvider();

$provider->register($di);

/** @var Queue $queue */
$queue = $di->getShared('queue');

$process = new QueueProcessHelper();

$queue->connect(new BeanstalkTube($process->getTube()));

//$queue->cleanupBuried();
//$queue->cleanUpQueue();

$queue->workReserved();

$queue->disconnect();