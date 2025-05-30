<?php

namespace Health\Bridge\Lumen;

use Health\StrSupport;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class HealthController extends BaseController
{
    /**
     * GET /health/status
     */
    public function status(Request $request)
    {
        return response()->json(['status' => 'success']);
    }

    /**
     * GET /health/logs
     */
    public function logs(Request $request)
    {
        return response()->json(['status' => 'not_implemented', 'data' => []]);
    }

    /**
     * GET /health/routes
     * 列出所有 Lumen 路由接口
     */
    public function routes()
    {
        return response()->json([
            'status' => 'success',
            'data' => self::allRoutes(),
        ]);
    }

    /**
     * 静态方法：获取所有路由，兼容数组/对象，controller和action分开
     * @return array
     */
    public static function allRoutes()
    {
        $routes = [];
        $globalMiddleware = method_exists(app(), 'getMiddleware') ? app()->getMiddleware() : [];
        // dd(app('router')->getRoutes());
        foreach (app('router')->getRoutes() as $route) {
            if (is_array($route)) {
                // Lumen 路由数组结构
                $method = $route['method'] ?? '';
                $uri = $route['uri'] ?? '';
                $action = $route['action'] ?? '';
                if (is_array($action)) {
                    $middleware = isset($action['middleware'])
                        ? (is_array($action['middleware']) ? $action['middleware'] : [$action['middleware']])
                        : [];
                    if (array_key_exists('as', $action)) {
                        $actionStr = $action['as'];
                    } else if (array_key_exists('uses', $action)) {
                        $actionStr = $action['uses'];
                    } else {
                        $middleware = [];
                        $actionStr = $action[0];
                    }
                } else {
                    $middleware = [];
                    $actionStr = is_string($action) ? $action : '';
                }
                // 拆分 controller/method
                $controller = '';
                $methodName = '';
                if (is_string($actionStr) && strpos($actionStr, '@') !== false) {
                    [$controller, $methodName] = explode('@', $actionStr, 2);
                } elseif ($actionStr instanceof \Closure) {
                    $controller = 'function';
                    $methodName = 'function';
                } else {
                    $controller = $actionStr;
                    $methodName = '';
                }
                $routes[] = [
                    'method' => is_array($method) ? implode('|', $method) : $method,
                    'uri' => $uri,
                    'controller' => $controller,
                    'action' => $methodName,
                    'name' => StrSupport::routerToName($uri),
                    'middleware' => implode(',', $middleware),
                    'global_middleware' => implode(',', $globalMiddleware),
                ];
            } elseif (is_object($route)) {
                // Laravel 路由对象
                $actionStr = $route->getActionName();
                $controller = '';
                $methodName = '';
                if (is_string($actionStr) && strpos($actionStr, '@') !== false) {
                    [$controller, $methodName] = explode('@', $actionStr, 2);
                } elseif ($actionStr instanceof \Closure) {
                    $controller = 'function';
                    $methodName = 'function';
                } else {
                    $controller = $actionStr;
                    $methodName = '';
                }
                $routes[] = [
                    'method' => implode('|', $route->methods()),
                    'uri' => $route->uri(),
                    'controller' => $controller,
                    'action' => $methodName,
                    'name' => $route->getName(),
                    'middleware' => implode(',', $route->gatherMiddleware()),
                    'global_middleware' => implode(',', $globalMiddleware),
                ];
            }
        }
      
        return $routes;
    }
}
