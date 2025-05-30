<?php

namespace Health\Bridge\Webman;

use Health\Core\HealthOrchestrator;
use Health\Core\Config;

class HealthPlugin
{
    /**
     * Webman 路由回调示例
     * GET /plugin/health/status
     */
    public static function status($request)
    {
        // 假设 config('plugin.health') 返回 health 配置
        $configArr = function_exists('config') ? config('plugin.health', []) : [];
        $checkerFactory = function ($class) {
            return new $class();
        };
        $manager = new HealthOrchestrator(new Config($configArr), $checkerFactory);
        $results = $manager->checkAll(false);
        return function_exists('json')
            ? json(['status' => 'success', 'data' => $results])
            : json_encode(['status' => 'success', 'data' => $results]);
    }
}
