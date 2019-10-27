<?php

namespace App\Providers;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '');

use App\Amqp\Worker;
use App\Events\EmailInvalidated;
use App\Events\EmailValidated;
use App\Events\UserEmailUpdated;
use App\Commands\UserUpdatedEmail;
use App\Listeners\ListenerContract;
use App\Listeners\UserTaskListener;
use App\Loggers\AmqpLogger;
use App\Loggers\LogStashLogger;
use App\Loggers\QueueLogger;
use App\Models\EmailValidation;
use App\Models\User;
use App\Queue\Queue;
use App\Reactors\EmailValidationReactor;
use App\Reactors\Reactor;
use App\Reactors\UserReactor;
use App\Repositories\UserRepository;
use App\Tasks\ProcessEventTask;
use App\Tasks\TaskConductor;
use App\ValueObjects\ListenerPriority;
use App\ValueObjects\ReactorPriority;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;
use Phalcon\Di;
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

        $this->di->setShared('config', function () {
            return include dirname(__DIR__) . "/Config/config.php";
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
            File::class,
            function() use($config) {
                $file = sprintf("%s/%s", $config->path("logging.file.path"), $config->path("logging.file.name"));

                if (! file_exists($file)) {
                    mkdir($config->path("logging.file.path"));
                    touch($file);
                }

                return new File($file);
            }
        );

        $this->di->setShared(
            LogStashLogger::class,
            function() use($config) {
                $name = $config->path("logging.logstash.name");
                $connection = sprintf("%s:%d", $config->path("logging.logstash.host"), $config->path("logging.logstash.port"));
                $type = $config->application->name;

                return new LogStashLogger(
                    new Logger($name),
                    new SocketHandler($connection),
                    new LogstashFormatter($type)
                );
            }
        );

        $this->di->setShared(
            EventsManager::class,
            function() {
                return new EventsManager();
            }
        );

        $eventsManager = $this->getEventsManager();

        $this->registerCommands();

        $this->registerEvents();

        $this->di->setShared(
            TaskConductor::class,
            function() use($di, $eventsManager) {
                $task = new ProcessEventTask($di);

                $task->setEventsManager($eventsManager);

                return new TaskConductor($task);
            }
        );

        $this->di->setShared(
            UserReactor::class,
            function() use($di) {
                return new UserReactor(
                    $di->get(File::class)
                );
            }
        );

        $this->di->setShared(
            EmailValidationReactor::class,
            function() use($di) {
                return new EmailValidationReactor(
                    $di->get(File::class)
                );
            }
        );

        $this->di->setShared(
            UserTaskListener::class,
            function() use($di) {
                return new UserTaskListener(
                    $di->get(File::class),
                    new UserRepository(
                        new User(),
                        new EmailValidation()
                    ),
                    $di->get(TaskConductor::class)
                );
            }
        );

        $this->di->setShared(
            Queue::class,
            function() use($di, $config) {
                /** @var LogStashLogger $logstash */
                $logstash = $di->getShared(LogStashLogger::class);

                $logger = new QueueLogger($config);

                /** @var TaskConductor $taskConductor */
                $taskConductor = $di->getShared(TaskConductor::class);

                $beanstalk = new Beanstalk(
                    [
                        'host' => $config->path("queue.beanstalkd.host"),
                        'port' => $config->path("queue.beanstalkd.port")
                    ]
                );

                return new Queue($beanstalk, $taskConductor, $logstash, $logger);
            }
        );

        $this->di->setShared(
            Worker::class,
            function() use($di, $config) {
                $host = $config->path("messaging.rabbitmq.host");
                $port = $config->path("messaging.rabbitmq.port");
                $user = $config->path("messaging.rabbitmq.user");
                $password = $config->path("messaging.rabbitmq.password");

                $connection = new AMQPStreamConnection($host, $port, $user, $password);

                /** @var LogStashLogger $logstash */
                $logstash = $di->getShared(LogStashLogger::class);

                $logger = new AmqpLogger($config);

                return new Worker(
                    $connection,
                    $di->getShared(Queue::class),
                    $logstash,
                    $logger
                );
            }
        );

        $this->registerListeners($eventsManager, $di, (array)$config->listeners);
    }

    private function getEventsManager() : EventsManager
    {
        return $this->di->getShared(EventsManager::class);
    }

    private function registerListeners(EventsManager $manager, DiInterface $di, array $listeners)
    {
        foreach($listeners as $listener) {

            $listener = $di->get($listener);

            foreach($listener::getEvents() as $event) {
                $manager->attach(
                    $this->getEventName($event),
                    $listener,
                    self::getListenerPriority($listener)
                );
            }
        }
    }

    private static function getListenerPriority(ListenerContract $listener) : int
    {
        if ($listener instanceof Reactor) {
            return ReactorPriority::toInteger();
        }

        return ListenerPriority::toInteger();
    }

    private function getEventName(string $event) : string
    {
        return ($event)::getBaseEventType();
    }

    private function registerCommands()
    {
        $this->di->set(
            UserUpdatedEmail::getUblName(),
            UserUpdatedEmail::class
        );
    }

    private function registerEvents()
    {
        $this->di->set(
            UserEmailUpdated::getUblName(),
            UserEmailUpdated::class
        );

        $this->di->set(
            EmailValidated::getUblName(),
            EmailValidated::class
        );

        $this->di->set(
            EmailInvalidated::getUblName(),
            EmailInvalidated::class
        );
    }
}