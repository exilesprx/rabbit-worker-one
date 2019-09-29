<?php

namespace App\Queue;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Exceptions\OutOfOrderException;
use App\Providers\ServiceProvider;
use App\tasks\TaskContract;
use App\Valueobjects\HighPriorityJob;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;
use Phalcon\Di\FactoryDefault;
use Phalcon\DiInterface;
use Phalcon\Logger\Adapter\File;
use Phalcon\Queue\Beanstalk;

$di = new FactoryDefault();
$provider = new ServiceProvider();

$provider->register($di);

$logger = new Logger("phalcon-one-worker");

$log = $di->getShared('logger');

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

$queue->watch('phalcon');
//findBuried($queue, $log);
while (($job = $queue->reserve(60))) {
//    $job->delete(); findBuried($queue, $log); continue;
    $log->info("Status: " . json_encode($job->stats()));

    $body = $job->getBody();

    $payload = json_decode($body, true);

    $logger->info("Worked-Event", $payload);

    $logger->close();

    $log->info("Worked: " . $body);

    try {
        $task = getTask($di, "process-event-task");

        $task->execute($payload);

        findBuried($queue, $log);

    } catch (OutOfOrderException $exception) {
        $log->critical($exception->getMessage());

        /**
         * Initial jobs have a LowPriorityJob value, therefore;
         * once a job is re-queued, set the priority to H * D * 100
         * Where:
         *      H = HighPriorityJob value
         *      D = Difference between current version and next version
         *      100 = Standard value
         */
        $job->bury(HighPriorityJob::getValue() * $exception->getDifference() * 100);

        continue;
    }

    $job->delete();
}

function getTask(DiInterface $di, string $name) : TaskContract
{
    return $di->get($name);
}

/**
 * @param Beanstalk $queue
 * @param File $log
 *
 * Find the jobs that were out of order and kick them back into the queue. This
 * occurs when an event has been received out of order.
 */
function findBuried(Beanstalk $queue, File $log)
{
    while(($job = $queue->peekBuried()) !== false)
    {
        $log->alert("Buried job kicked out of buried queue for reprocessing.");

        $job->kick();
    }
}

$queue->disconnect();