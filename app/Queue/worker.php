<?php

namespace App\Queue;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Providers\ServiceProvider;
use App\tasks\TaskContract;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;
use Phalcon\Di\FactoryDefault;
use Phalcon\DiInterface;
use Phalcon\Queue\Beanstalk;

$di = new FactoryDefault();
$provider = new ServiceProvider();

$provider->register($di);

$logger = new Logger("phalcon-one-worker");

$handler = new SocketHandler("logstash:5055");

$formatter = new LogstashFormatter("phalcon");

$handler->setFormatter($formatter);

$logger->pushHandler($handler);

$queue = new Beanstalk(
    [
        'host' => 'beanstalkd',
        'port' =>11300
    ]
);

$queue->connect();

$queue->choose("phalcon");

function getTask(DiInterface $di, string $name) : TaskContract
{
    return $di->get($name);
}

while(true) {
    while (($job = $queue->peekReady()) !== false) {
        $body = $job->getBody();

        $payload = json_decode($body, true);

        $logger->info("Worked-Event", $payload);

        $logger->close();

        $task = getTask($di, "process-event-task");

        $task->execute($payload);

        $job->delete();
    }
}

$queue->disconnect();