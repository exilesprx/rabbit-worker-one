<?php

namespace App\Providers;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '');

use App\events\UserUpdatedEmail;
use App\Listeners\UserTaskListener;
use App\Tasks\ProcessEventTask;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Db\Adapter\Pdo\Mysql;

class ServiceProvider implements ServiceProviderInterface
{
    /** @var DiInterface $di */
    protected $di;

    /**
     * Registers a service provider.
     *
     * @param \Phalcon\DiInterface $di
     */
    public function register(DiInterface $di)
    {
        $this->di = $di;

        $this->di->setShared('config', function () {
            return include APP_PATH . "/config/config.php";
        });

        $config = $di->get('config');

        $this->di->setShared('db', function () use ($config) {

            $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
            $params = [
                'host'     => $config->database->host,
                'username' => $config->database->username,
                'password' => $config->database->password,
                'dbname'   => $config->database->dbname,
                'charset'  => $config->database->charset
            ];

            if ($config->database->adapter == 'Postgresql') {
                unset($params['charset']);
            }

            $connection = new $class($params);

            return $connection;
        });

        $this->di->setShared(
            'logger',
            function() {
                return new File('log.log');
            }
        );

        $this->di->setShared(
            UserTaskListener::class,
            function() use($di) {
                return new UserTaskListener(
                    $di->get('logger')
                );
            }
        );

        $this->di->setShared(
            'event-manager',
            function() {
                return new EventsManager();
            }
        );

        $eventsManager = $this->getEventsManager();

        $this->di->set(
            'user.updated.email',
            UserUpdatedEmail::class
        );

        $this->di->set(
            'process-event-task',
            function() use($eventsManager, $di) {
                $task = new ProcessEventTask($di);

                $task->setEventsManager($eventsManager);

                return $task;
            }
        );

        $this->di->setShared(
            'amqp',
            function() {
                return $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
            }
        );

        $this->registerListeners($eventsManager, $di, (array)$config->listeners);
    }

    private function getEventsManager() : EventsManager
    {
        return $this->di->getShared('event-manager');
    }

    private function registerListeners(EventsManager $manager, DiInterface $di, array $listeners)
    {
        foreach($listeners as $listener) {

            $listener = $di->get($listener);

            foreach($listener::getEvents() as $event) {
                $manager->attach(
                    $event,
                    $listener
                );
            }
        }
    }
}