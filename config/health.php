<?php

return [
    'enabled' => env('HEALTH_ENABLED', true),

    'services' => [
        // 示例（推荐新写法，自动识别检查类型）
        // [
        //     'check' => \App\Services\Health::MYSQL,
        //     'name' => 'mysql',
        //     'connection' => 'mysql',
        //     'timeout' => 2,
        // ],
        // [
        //     'check' => \App\Services\Health::REDIS,
        //     'name' => 'redis',
        //     'connection' => 'default',
        //     'timeout' => 2,
        // ],
        // [
        //     'check' => \App\Services\Health::OSS,
        //     'name' => 'oss',
        //     'access_key' => env('OSS_ACCESS_KEY'),
        //     'secret_key' => env('OSS_SECRET_KEY'),
        //     'bucket' => env('OSS_BUCKET'),
        //     'endpoint' => env('OSS_ENDPOINT'),
        //     'timeout' => 3,
        // ],
        // [
        //     'check' => \App\Services\Health::HTTP,
        //     'name' => 'external-api',
        //     'url' => 'https://api.example.com/health',
        //     'method' => 'GET',
        //     'headers' => [],
        //     'timeout' => 3,
        //     'auth' => [
        //         'type' => 'token',
        //         'key' => env('API_TOKEN'),
        //     ],
        // ],
        // 兼容旧写法（type 字段）
    ],

    'routes' => [
        'include' => [
            // '/api/*'
        ],
        'exclude' => [
            // '/api/health*'
        ],
    ],

    'log' => [
        'table' => 'health_call_logs',
        'retention_days' => 30,
    ],

    'alert' => [
        'mail' => [
            'enabled' => false,
            'to' => env('HEALTH_ALERT_MAIL_TO'),
        ],
        'slack' => [
            'enabled' => false,
            'webhook_url' => env('HEALTH_ALERT_SLACK_WEBHOOK'),
        ],
        'webhook' => [
            'enabled' => false,
            'url' => env('HEALTH_ALERT_WEBHOOK_URL'),
        ],
        'threshold' => [
            'down' => 1,
            'timeout' => 3,
        ],
    ],
];
