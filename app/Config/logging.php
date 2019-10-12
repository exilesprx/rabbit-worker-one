<?php

return [
    'logstash' => [
        "name" => getenv('LOGSTASH_NAME'),
        "host" => getenv('LOGSTASH_HOST'),
        "port" => getenv('LOGSTASH_PORT')
    ],

    'file' => [
        "name" => getenv('LOG_NAME'),
        "path" => BASE_PATH . "/logs"
    ]
];