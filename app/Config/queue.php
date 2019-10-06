<?php

return [
    "beanstalkd" => [
        "host" => getenv('QUEUE_HOST'),
        "port" => getenv('QUEUE_PORT')
    ]
];