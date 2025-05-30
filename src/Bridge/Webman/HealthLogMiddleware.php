<?php

namespace Health\Bridge\Webman;

use Webman\MiddlewareInterface;
use Webman\Http\Request;
use Webman\Http\Response;
use support\Db;
use Carbon\Carbon;

class HealthLogMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        $start = microtime(true);
        $response = $next($request);
        $latency = round((microtime(true) - $start) * 1000, 2);

        $uri = $request->path();
        $method = $request->method();
        $params = $request->all();
        $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 0;
        $user = null; // 可集成 RBAC
        $ip = $request->getRealIp();

        $table = config('plugin.health.log.table', 'health_call_logs');
        Db::table($table)->insert([
            'service' => $uri,
            'status' => $status,
            'latency' => $latency,
            'message' => json_encode([
                'method' => $method,
                'params' => $params,
            ]),
            'user' => $user,
            'ip' => $ip,
            'checked_at' => Carbon::now()->toDateTimeString(),
        ]);

        return $response;
    }
}
