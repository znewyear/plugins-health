<?php

use Health\Core\HealthOrchestrator;
use Health\Core\Config;


$config = [
'enabled' => true,
    'services' => [
        // ...
    ],
    'log' => [
        'table' => 'health_call_logs',
        'retention_days' => 30,
    ],
];

return array_merge($config, [
    // 全局 orchestrator 实例（Webman 推荐）
    'orchestrator' => new HealthOrchestrator(
        new Config($config),
        function ($class) { return new $class(); }
    ),
]);
