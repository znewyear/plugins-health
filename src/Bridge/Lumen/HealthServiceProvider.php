<?php

namespace Health\Bridge\Lumen;

use Illuminate\Support\ServiceProvider;
use Health\Core\HealthOrchestrator;
use Health\Core\Config;

class HealthServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Lumen 需手动 $app->configure('health') 并加载 config/health.php
        $this->app->singleton(HealthOrchestrator::class, function ($app) {
            $config = $app->make('config')->get('health', []);
            $checkerFactory = function ($class) use ($app) {
                return $app->make($class);
            };
            return new HealthOrchestrator(new Config($config), $checkerFactory);
        });

        $this->commands([
            \Health\Bridge\Laravel\Console\HealthCheckCommand::class,
            \Health\Bridge\Lumen\Console\ListRoutesCommand::class,
        ]);

        // 直接注册路由，无需 require 路由文件
        $router = $this->app->router;
        $router->group(['prefix' => 'health', 'namespace' => 'Health\Bridge\Lumen'], function () use ($router) {
            $router->get('status', 'HealthController@status');
            $router->get('logs', 'HealthController@logs');
            $router->get('routes', 'HealthController@routes');
        });
    }
}
