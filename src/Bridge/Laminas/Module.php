<?php

namespace Health\Bridge\Laminas;

use Health\Core\HealthOrchestrator;
use Health\Core\Config;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../../../config/health.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                HealthOrchestrator::class => function ($container) {
                    $config = $container->get('config')['health'] ?? [];
                    $checkerFactory = function ($class) use ($container) {
                        return $container->get($class);
                    };
                    return new HealthOrchestrator(new Config($config), $checkerFactory);
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                HealthController::class => function ($container) {
                    return new HealthController($container->get(HealthOrchestrator::class));
                },
            ],
        ];
    }
}
