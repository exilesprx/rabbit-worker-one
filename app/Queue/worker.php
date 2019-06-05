<?php

namespace App\Queue;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Providers\ServiceProvider;
use Phalcon\Di\FactoryDefault;
use Phalcon\Queue\Beanstalk;

$di = new FactoryDefault();
$provider = new ServiceProvider();

$provider->register($di);

$queue = new Beanstalk(
    [
        'host' => 'beanstalkd',
        'port' =>11300
    ]
);

$queue->connect();

while(true) {
    while (($job = $queue->peekReady()) !== false) {
        $payload = json_decode($job->getBody(), true);

        $task = $di->get($payload['name']);

        $task->execute($payload['payload']);

        $job->delete();
    }

    usleep(100);
}

$queue->disconnect();