<?php


namespace App\Entities;


use App\Tasks\Task;
use App\Tasks\TaskCollection;

trait RecordsEvents
{
    /** @var TaskCollection */
    protected $events;

    public function recordTask(Task $task)
    {
        $this->events->addTask($task);
    }

    public function hasTasks() : bool
    {
        // First, check the current instance.
        if ($this->events->hasTasks()) {
            return true;
        }

        $properties = get_object_vars($this);

        foreach(array_values($properties) as $property) {
            // Ignore properties that are not entities
            if (!$property instanceof Entity && !$property instanceof EventableEntityContract) {
                continue;
            }

            // This becomes a recursive check down the entity tree
            if ($property->hasTasks()) {
                return true;
            }
        }
        return false;
    }
}