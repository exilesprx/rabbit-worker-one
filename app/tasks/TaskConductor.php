<?php

namespace App\Tasks;

use App\Entities\QueueJobBody;
use App\Exceptions\OutOfOrderException;

class TaskConductor
{
    protected $task;

    public function __construct(ProcessEventTask $task)
    {
        $this->task = $task;
    }

    /**
     * @param QueueJobBody $payload
     * @throws OutOfOrderException
     */
    public function executeTask(QueueJobBody $payload)
    {
        $this->task->execute($payload->toArray());
    }
}