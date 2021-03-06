<?php

use App\Tasks\Task;
use App\Tasks\TaskCollection;
use Faker\Factory;

class TaskCollectionTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var \Faker\Generator */
    protected $faker;

    /** @var TaskCollection */
    protected $collection;

    protected function _before()
    {
        $this->faker = Factory::create();

        $this->collection = new TaskCollection();
    }

    // tests
    public function testTaskCollectionIsEmpty()
    {
        $this->assertFalse($this->collection->hasTasks());
    }

    public function testTaskCollectionHasNoTasks()
    {
        $this->tester->assertFalse($this->collection->hasTasks());
    }

    public function testTaskCollectionHasOnePendingTask()
    {
        $this->collection->addTask($this->generateNewTask());

        $this->tester->assertCount(1, $this->collection);
    }

    public function testTestCollectionHasFivePendingTasks()
    {
        $this->collection->addTask($this->generateNewTask());
        $this->collection->addTask($this->generateNewTask());
        $this->collection->addTask($this->generateNewTask());
        $this->collection->addTask($this->generateNewTask());
        $this->collection->addTask($this->generateNewTask());

        $this->tester->assertCount(5, $this->collection);
    }

    public function testTaskCollectionIsEmptyAfterFlushingTasks()
    {
        $this->collection->addTask($this->generateNewTask());
        $this->collection->flush();

        $this->tester->assertCount(0, $this->collection);
        $this->tester->assertTrue($this->collection->isEmpty());
    }

    protected function generateNewTask() : Task
    {
        return new Task(
            $this->faker->name,
            []
        );
    }
}