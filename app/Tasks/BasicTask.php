<?php


namespace App\Tasks;


use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;

class BasicTask implements EventsAwareInterface
{
    protected $manager;

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
    public function getEventsManager() : ManagerInterface
    {
        return $this->manager;
    }

    public function execute(array $data)
    {
        $this->manager->fire('basic-task:handle', $this, $data);
    }
}