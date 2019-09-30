<?php

namespace App\Queue;

use App\Entities\QueueJobBody;
use App\Exceptions\OutOfOrderException;
use App\Loggers\LogStashLogger;
use App\Tasks\TaskConductor;
use App\ValueObjects\BeanstalkTube;
use App\ValueObjects\BuriedJobPriority;
use App\ValueObjects\LowPriorityJob;
use Phalcon\Logger\Adapter\File;
use Phalcon\Queue\Beanstalk;
use PhpAmqpLib\Message\AMQPMessage;

class Queue
{
    private static $TIMEOUT = 60;

    private $beanstalk;

    private $taskConductor;

    private $logger;

    private $file;

    public function __construct(Beanstalk $beanstalk, TaskConductor $taskConductor, LogStashLogger $logger, File $file)
    {
        $this->beanstalk = $beanstalk;

        $this->taskConductor = $taskConductor;

        $this->logger = $logger;

        $this->file = $file;
    }

    public function connect(BeanstalkTube $tube)
    {
        $this->beanstalk->connect();

        $this->beanstalk->watch((string)$tube);

        $this->beanstalk->choose((string)$tube);
    }

    public function putLowPriorityJob(AMQPMessage $message)
    {
        $this->beanstalk->put(
            $message->body,
            [
                'priority' => LowPriorityJob::getValue()
            ]
        );
    }

    public function workReserved()
    {
        while (($job = $this->beanstalk->reserve(self::$TIMEOUT)) !== false) {
            $body = QueueJobBody::from($job);

            try {
                $this->taskConductor->executeTask($body);

                $this->workBuriedJobsOfSameType($body);
            } catch (OutOfOrderException $exception) {
                $this->file->critical($exception->getMessage());

                /**
                 * Initial jobs have a LowPriorityJob value, therefore;
                 * once a job is re-queued, set the priority to BuriedJobPriority
                 */
                $job->bury(BuriedJobPriority::getValue($exception));

                continue;
            }

            $job->delete();

            $this->logger->info("Worked-Event", $body->toArray());
        }
    }

    protected function workBuriedJobsOfSameType(QueueJobBody $queueJobBody)
    {
        while(($job = $this->beanstalk->peekBuried()) !== false)
        {
            $body = QueueJobBody::from($job);

            if ($body->isSameType($queueJobBody)) {
                $this->file->alert(sprintf("Kicking job version %d of type %s back into queue for reprocessing.", $body->getVersion(), $body->getType()));

                $job->kick();
            }
        }
    }

    public function cleanUpQueue()
    {
        while(($job = $this->beanstalk->peekReady()) !== false)
        {
            $this->file->error("Cleanup up jobs");
            $job->delete();
        }
    }
    public function cleanupBuried()
    {
        while(($job = $this->beanstalk->peekBuried()) !== false)
        {
            $this->file->error("Cleaning up buried jobs");
            $job->delete();
        }
    }

    public function disconnect()
    {
        $this->beanstalk->disconnect();
    }
}