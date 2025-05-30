<?php

// Laminas 路由注册示例，放入 module.config.php
return [
    'router' => [
        'routes' => [
            'health-status' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/health/status',
                    'defaults' => [
                        'controller' => \Health\Bridge\Laminas\HealthController::class,
                        'action'     => 'status',
                    ],
                ],
            ],
        ],
    ],
];
