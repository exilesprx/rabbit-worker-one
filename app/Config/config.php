<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

$env = \Dotenv\Dotenv::create(dirname(BASE_PATH . "../"));
$env->load();

return new \Phalcon\Config([
    'database' => require_once('database.php'),

    'application' => require_once('application.php'),

    'queue' => require_once('queue.php'),

    'messaging' => require_once('messaging.php'),

    'listeners' => require_once('listeners.php'),

    'logging' => require_once('logging.php')
]);
