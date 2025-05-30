<?php

/** @var \Laravel\Lumen\Routing\Router $router */

// 兼容 $router 未定义
$router = $router ?? app('router');

$router->group(['prefix' => 'health', 'namespace' => 'Health\Bridge\Lumen'], function () use ($router) {
    $router->get('status', 'HealthController@status');
    $router->get('logs', 'HealthController@logs');
});
