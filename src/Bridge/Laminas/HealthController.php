<?php

namespace Health\Bridge\Laminas;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Health\Core\HealthOrchestrator;

class HealthController extends AbstractActionController
{
    protected $orchestrator;

    public function __construct(HealthOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    public function statusAction()
    {
        $results = $this->orchestrator->checkAll(false);
        return new JsonModel([
            'status' => 'success',
            'data' => $results,
        ]);
    }

    /**
     * GET /health/routes
     * 列出所有 Laminas 路由接口
     */
    public function routesAction()
    {
        $router = $this->getEvent()->getApplication()->getServiceManager()->get('Router');
        $routes = [];
        foreach ($router->getRoutes() as $name => $route) {
            $routes[] = [
                'name' => $name,
                'type' => get_class($route),
                'route' => method_exists($route, 'getRoute') ? $route->getRoute() : '',
                'defaults' => json_encode(method_exists($route, 'getDefaults') ? $route->getDefaults() : []),
            ];
        }
        return new JsonModel([
            'status' => 'success',
            'data' => $routes,
        ]);
    }
}
