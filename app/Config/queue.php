<?php

return [
    "beanstalkd" => [
        "host" => getenv('QUEUE_HOST'),
        "port" => getenv('QUEUE_PORT'),
        "tube" => getenv('QUEUE_DEFAULT_TUBE')
    ]
];