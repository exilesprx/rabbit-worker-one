<?php

namespace App\Providers;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '');

use App\Tasks\BasicTask;
use App\Listeners\BasicTaskListener;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Db\Adapter\Pdo\Mysql;

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

        $this->di->setShared('config', function () {
            return include APP_PATH . "/config/config.php";
        });

        $this->di->setShared('db', function () use ($di) {
            $config = $di->get('config');

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

        $this->di->setShared(
            'amqp',
            function() {
                return $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
            }
        );

        $eventsManager->attach(
            'basic-task',
            new BasicTaskListener(
                $this->di->getShared('logger')
            )
        );
    }

    private function getEventsManager() : EventsManager
    {
        return $this->di->getShared('event-manager');
    }
}