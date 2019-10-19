<?php

namespace App\Tasks;

use App\Events\EventContract;
use Phalcon\DiInterface;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;

class ProcessEventTask implements EventsAwareInterface, TaskContract
{
    /** @var ManagerInterface $manager */
    protected $manager;

    protected $di;

    /**
     * @param DiInterface $di
     */
    public function __construct(DiInterface $di)
    {
        $this->di = $di;
    }

    /**
     * Sets the tasks manager
     *
     * @param ManagerInterface $eventsManager
     */
    public function setEventsManager(ManagerInterface $eventsManager)
    {
        $this->manager = $eventsManager;
    }

    /**
     * Returns the internal event manager
     *
     * @return ManagerInterface
     */
    public function getEventsManager(): ManagerInterface
    {
        return $this->manager;
    }

    public function execute(string $name, array $data)
    {
        $event = $this->getEvent($name, $data);

        $this->manager->fire($event::getEventType(), $this, $data);
    }

    public function getEvent(string $name, array $data) : EventContract
    {
        return $this->di->get($name, [$data]);
    }
}