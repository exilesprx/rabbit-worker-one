<?php

namespace App\Tasks;

use Iterator;

class TaskCollection implements Iterator
{
    private $tasks;

    private $position;

    public function __construct(array $tasks = [])
    {
        $this->tasks = $tasks;

        $this->position = 0;
    }

    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
    }

    public function flush() : TaskCollection
    {
        $tasks = new self($this->tasks);

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

    public function current() : Task
    {
        return $this->tasks[$this->position];
    }

    public function next()
    {
        $this->position++;
    }

    public function key() : int
    {
        return $this->position;
    }

    public function valid() : bool
    {
        return isset($this->tasks[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }
}