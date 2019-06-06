<?php

namespace App\Queue;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Providers\ServiceProvider;
use App\tasks\TaskContract;
use Phalcon\Di\FactoryDefault;
use Phalcon\DiInterface;
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

function getTask(DiInterface $di, string $name) : TaskContract
{
    return $di->get($name);
}

while(true) {
    while (($job = $queue->peekReady()) !== false) {
        $payload = json_decode($job->getBody(), true);

        $task = getTask($di, $payload['name']);

        $task->execute($payload);

        $job->delete();
    }

    usleep(100);
}

$queue->disconnect();