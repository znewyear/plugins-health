<?php

namespace Health\Bridge\Laravel;

use Illuminate\Support\ServiceProvider;
use Health\Core\HealthOrchestrator;
use Health\Core\Config;

class HealthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 发布配置
        $this->publishes([
            __DIR__ . '/../../../config/health.php' => config_path('health.php'),
        ], 'config');

        // 路由注册
        if (method_exists($this, 'loadRoutesFrom')) {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        }

        // 视图注册（Blade）
        if (method_exists($this, 'loadViewsFrom')) {
            $this->loadViewsFrom(__DIR__ . '/../../../resources/views', 'health');
        }

        // 数据库迁移
        if (method_exists($this, 'loadMigrationsFrom')) {
            $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');
        }
    }

    public function register()
    {
        // 绑定 HealthOrchestrator，注入自有 Config
        $this->app->singleton(HealthOrchestrator::class, function ($app) {
            $config = $app->make('config')->get('health', []);
            // checkerFactory: 统一由容器实例化
            $checkerFactory = function ($class) use ($app) {
                return $app->make($class);
            };
            return new HealthOrchestrator(new Config($config), $checkerFactory);
        });

        $this->commands([
            \Health\Bridge\Laravel\Console\HealthCheckCommand::class,
            \Health\Bridge\Laravel\Console\ListRoutesCommand::class,
        ]);
    }
}
