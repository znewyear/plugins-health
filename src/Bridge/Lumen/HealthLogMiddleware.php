<?php

namespace Health\Bridge\Lumen;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HealthLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $latency = round((microtime(true) - $start) * 1000, 2);

        // 只记录 API 路由
        $uri = $request->path();
        $method = $request->method();
        $params = $request->all();
        $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 0;

        $table = config('health.log.table', 'health_call_logs');

        $ip = $request->ip();
   
        $user = Auth::check() ? Auth::id() : null;
   

        DB::table($table)->insert([
            'service' => $uri,
            'status' => $status,
            'latency' => $latency,
            'message' => json_encode([
                'method' => $method,
                'params' => $params,
            ]),
            'user' => $user,
            'ip' => $ip,
            'checked_at' => Carbon::now(),
        ]);

        return $response;
    }
}
