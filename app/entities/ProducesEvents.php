<?php

namespace App\Entities;

use App\Tasks\TaskCollection;
use App\Tasks\TaskConductor;

trait ProducesEvents
{
    /** @var TaskCollection */
    protected $events;

    public function recordEvents(TaskConductor $conductor)
    {
        $conductor->executeTasks($this->events->flush());

        $properties = get_object_vars($this);

        foreach(array_values($properties) as $property) {
            if ($property instanceof Entity && $property instanceof EventableEntityContract) {
                $property->recordEvents($conductor);
            }
        }
    }
}