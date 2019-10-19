<?php
putenv('APPLICATION_ENV=testing');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$di = \Phalcon\Di\FactoryDefault::getDefault();

if (is_null($di)) {
    $di = new Phalcon\Di();

    \Phalcon\Di\FactoryDefault::setDefault($di);
}

include dirname(__DIR__) . "/app/Config/services.php";

$config = include dirname(__DIR__) . "/app/Config/config.php";

$di->setShared('config', function () use ($config) {
    return $config;
});

include dirname(__DIR__) . "/app/Config/loader.php";

return new \Phalcon\Mvc\Application($di);