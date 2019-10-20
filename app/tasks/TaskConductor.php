<?php

namespace App\Tasks;

use App\Exceptions\OutOfOrderException;

class TaskConductor
{
    protected $task;

    public function __construct(ProcessEventTask $task)
    {
        $this->task = $task;
    }

    /**
     * @param Task $task
     * @throws OutOfOrderException
     */
    public function executeTask(Task $task)
    {
        $this->task->execute($task->getName(), $task->getPayload());
    }

    /**
     * @param TaskCollection $tasks
     * @throws OutOfOrderException
     */
    public function executeTasks(TaskCollection $tasks)
    {
        foreach($tasks as $task)
        {
            $this->executeTask($task);
        }

        $tasks->flush();
    }
}