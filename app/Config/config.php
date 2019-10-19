<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

$envFile = sprintf("%s%s", ".env", getenv('APPLICATION_ENV') ? "." . getenv('APPLICATION_ENV') : "");

$env = \Dotenv\Dotenv::create(dirname(dirname(__DIR__)), $envFile);
$env->load();

return new \Phalcon\Config([
    'database' => require(dirname(__FILE__) . '/database.php'),

    'application' => require(dirname(__FILE__) . '/application.php'),

    'queue' => require(dirname(__FILE__) . '/queue.php'),

    'messaging' => require(dirname(__FILE__) . '/messaging.php'),

    'listeners' => require(dirname(__FILE__) . '/listeners.php'),

    'logging' => require(dirname(__FILE__) . '/logging.php')
]);
