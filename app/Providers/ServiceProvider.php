<?php

namespace App\Providers;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '');

use App\Amqp\Worker;
use App\Events\UserUpdatedEmail;
use App\Listeners\UserTaskListener;
use App\Loggers\LogStashLogger;
use App\Queue\Queue;
use App\Tasks\ProcessEventTask;
use App\Tasks\TaskConductor;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File;
use Phalcon\Queue\Beanstalk;
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

        $this->di->setShared('Config', function () {
            return include APP_PATH . "/Config/config.php";
        });

        $config = $di->get('Config');

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
            'logstash-log',
            function() use($config) {
                return new LogStashLogger(
                    new Logger($config->logging->logstash->name),
                    new SocketHandler(sprintf("%s:%d", $config->logging->logstash->host, $config->logging->logstash->port)),
                    new LogstashFormatter($config->logging->logstash->application)
                );
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

        $this->di->setShared(
            'task-conductor',
            function() use($di, $eventsManager) {
                $task = new ProcessEventTask($di);

                $task->setEventsManager($eventsManager);

                return new TaskConductor($task);
            }
        );

        $this->di->setShared(
            'queue',
            function() use($di, $config) {
                /** @var LogStashLogger $logstash */
                $logstash = $di->getShared('logstash-log');

                /** @var File $file */
                $file = $di->getShared('logger');

                /** @var TaskConductor $taskConductor */
                $taskConductor = $di->getShared('task-conductor');

                $beanstalk = new Beanstalk(
                    [
                        'host' => $config->queue->beanstalkd->host,
                        'port' => $config->queue->beanstalkd->port
                    ]
                );

                return new Queue($beanstalk, $taskConductor, $logstash, $file);
            }
        );

        $this->di->setShared(
            'amqp-worker',
            function() use($di, $config) {
                $host = $config->messaging->rabbitmq->host;
                $port = $config->messaging->rabbitmq->port;
                $user = $config->messaging->rabbitmq->user;
                $password = $config->messaging->rabbitmq->password;

                $connection = new AMQPStreamConnection($host, $port, $user, $password);

                /** @var LogStashLogger $logstash */
                $logstash = $di->getShared('logstash-log');

                /** @var File $file */
                $file = $di->getShared('logger');

                return new Worker(
                    $connection,
                    $di->getShared('queue'),
                    $logstash,
                    $file
                );
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
                    $this->getEventName($event),
                    $listener
                );
            }
        }
    }

    private function getEventName(string $event) : string
    {
        return ($event)::getBaseEventType();
    }
}