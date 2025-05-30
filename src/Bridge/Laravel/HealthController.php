<?php

namespace Health\Bridge\Laravel;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Health\Core\HealthOrchestrator;

class HealthController extends BaseController
{
    protected $orchestrator;

    public function __construct(HealthOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * GET /health/status
     */
    public function status(Request $request)
    {
        $batch = $request->input('batch', false);
        $results = $this->orchestrator->checkAll($batch);
        return response()->json([
            'status' => 'success',
            'data' => $results,
        ]);
    }

    /**
     * GET /health/logs
     */
    public function logs(Request $request)
    {
        // 日志查询建议由业务项目实现
        return response()->json([
            'status' => 'not_implemented',
            'data' => [],
        ]);
    }

    /**
     * GET /health/routes
     * 列出所有路由接口
     */
    public function routes()
    {
        $routes = [];
        foreach (app('router')->getRoutes() as $route) {
            $routes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'action' => $route->getActionName(),
                'name' => $route->getName(),
                'middleware' => $route->gatherMiddleware(),
            ];
        }
        return response()->json([
            'status' => 'success',
            'data' => $routes,
        ]);
    }
}
