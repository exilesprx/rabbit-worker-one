<?php


namespace App\Providers;


use App\Tasks\BasicTask;
use App\Listeners\BasicTaskListener;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File;

class ServiceProvider implements ServiceProviderInterface
{
    protected $di;

    /**
     * Registers a service provider.
     *
     * @param \Phalcon\DiInterface $di
     */
    public function register(\Phalcon\DiInterface $di)
    {
        $this->di = $di;

        $this->di->setShared(
            'event-manager',
            function() {
                return new EventsManager();
            }
        );

        $eventsManager = $this->getEventsManager();

        $this->di->set(
            'basic.task',
            function() use($eventsManager) {
                $task = new BasicTask();

                $task->setEventsManager($eventsManager);

                return $task;
            }
        );

        $eventsManager->attach('basic-task', new BasicTaskListener(new File('log.log')));
    }

    private function getEventsManager() : EventsManager
    {
        return $this->di->getShared('event-manager');
    }
}