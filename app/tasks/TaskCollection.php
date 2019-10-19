<?php

namespace App\Tasks;

class TaskCollection
{
    private $tasks;

    public function __construct()
    {
        $this->tasks = [];
    }

    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
    }

    public function getTasks() : array
    {
        $tasks = $this->tasks;

        $this->tasks = [];

        return $tasks;
    }

    public function isEmpty() : bool
    {
        return empty($this->tasks);
    }

    public function hasTasks() : bool
    {
        return !empty($this->tasks);
    }
}