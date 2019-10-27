<?php

namespace App\Entities;

use App\Tasks\Task;
use App\Tasks\TaskConductor;

interface EventableEntityContract
{
    public function recordEvents(TaskConductor $conductor);

    public function recordTask(Task $task);

    public function hasTasks() : bool;
}