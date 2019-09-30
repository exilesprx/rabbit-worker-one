<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'adapter'    => 'Mysql',
        'host'       => 'mariadb',
        'username'   => 'default',
        'password'   => 'secret',
        'dbname'     => 'default',
        'charset'    => 'utf8',
    ],

    'application' => [
        'modelsDir'      => APP_PATH . '/Models/',
        'migrationsDir'  => APP_PATH . '/Migrations/',
        'viewsDir'       => APP_PATH . '/Views/',
        'baseUri'        => '/phalcon-one/',
    ],

    'queue' => require_once(APP_PATH . '/Config/queue.php'),

    'messaging' => require_once(APP_PATH . '/Config/messaging.php'),

    'listeners' => require_once(APP_PATH . '/Config/listeners.php'),

    'logging' => require_once(APP_PATH . '/Config/logging.php')
]);
