<?php

return [
    'adapter'    => getenv('DATABASE_ADAPTER'),
    'host'       => getenv('DATABASE_HOST'),
    'username'   => getenv('DATABASE_USER'),
    'password'   => getenv('DATABASE_PASSWORD'),
    'dbname'     => getenv('DATABASE_TABLE'),
    'charset'    => 'utf8',
];