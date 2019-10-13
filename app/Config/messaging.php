<?php

return [
    "rabbitmq" => [
        "host" => getenv('MESSAGE_BUS_HOST'),
        "port" => getenv('MESSAGE_BUS_PORT'),
        "user" => getenv('MESSAGE_BUS_USER'),
        "password" => getenv('MESSAGE_BUS_PASSWORD'),
        "queue" => getenv('MESSAGE_BUS_DEFAULT_QUEUE'),
        "exchange" => getenv('MESSAGE_BUS_DEFAULT_EXCHANGE')
    ]
];