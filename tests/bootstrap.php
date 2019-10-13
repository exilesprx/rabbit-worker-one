<?php
putenv('APPLICATION_ENV=testing');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$config = include __DIR__ . "../app/Config/config.php";
include __DIR__ . "../app/Config/loader.php";
$di = new \Phalcon\DI\FactoryDefault();
include __DIR__ . "../app/Config/services.php";
return new \Phalcon\Mvc\Application($di);