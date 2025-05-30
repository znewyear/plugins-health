<?php

namespace Health\Bridge\Lumen\Console;

use Illuminate\Console\Command;
use Health\Bridge\Lumen\HealthController;

class ListRoutesCommand extends Command
{
    protected $signature = 'health:routes';
    protected $description = '列出所有 Lumen 路由接口';

    public function handle()
    {
        $routes = HealthController::allRoutes();
      
        $this->table(
            ['Method', 'URI', 'Controller','Action', 'Name', 'Middleware', 'GlobalMiddleware'],
            $routes
        );
    }
}
