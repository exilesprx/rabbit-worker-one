<?php

return [
    "rabbitmq" => [
        "host" => getenv('MESSAGE_BUS_HOST'),
        "port" => getenv('MESSAGE_BUS_PORT'),
        "user" => getenv('MESSAGE_BUS_USER'),
        "password" => getenv('MESSAGE_BUS_PASSWORD')
    ]
];