<?php

namespace Health\Bridge\Webman;

use support\Request;

class HealthController
{
    /**
     * GET /plugin/health/routes
     * 列出所有 Webman 路由接口
     */
    public function routes(Request $request)
    {
        $routes = [];
        foreach (\Webman\Route::getRoutes() as $method => $items) {
            foreach ($items as $route) {
                $routes[] = [
                    'method' => $method,
                    'path' => $route->getPath(),
                    'callback' => is_array($route->getCallback()) ? implode('@', $route->getCallback()) : (string)$route->getCallback(),
                    'middleware' => implode(',', $route->getMiddleware()),
                ];
            }
        }
        return json(['status' => 'success', 'data' => $routes]);
    }
}
