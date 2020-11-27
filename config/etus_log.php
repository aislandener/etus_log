<?php

return [
    'enable' => env("ETUS_LOG_ENABLE", false),
    'aws' => [
        "key_id" => env('ETUS_LOG_AWS_ACCESS_KEY'),
        "secret_key" => env('ETUS_LOG_AWS_SECRET_KEY'),
        "region" => env('ETUS_LOG_REGION'),
        "version" => env("ETUS_LOG_DYNAMODB_VERSION", 'latest'),
        "table" => env('ETUS_LOG_DYNAMO_TABLE_NAME')
    ]
];