<?php

namespace Health\Bridge\Laravel\Console;

use Illuminate\Console\Command;

class ListRoutesCommand extends Command
{
    protected $signature = 'health:routes';
    protected $description = '列出所有 Lumen 路由接口';

    public function handle()
    {
        $routes = [];
        foreach (app('router')->getRoutes() as $route) {
            $routes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'action' => $route->getActionName(),
                'name' => $route->getName(),
                'middleware' => implode(',', $route->gatherMiddleware()),
            ];
        }
        $this->table(
            ['Method', 'URI', 'Action', 'Name', 'Middleware'],
            $routes
        );
    }
}
