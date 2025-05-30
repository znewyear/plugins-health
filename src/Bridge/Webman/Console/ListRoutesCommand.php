<?php

namespace Health\Bridge\Webman\Console;

use support\Console\Command;
use support\Console\Input;
use support\Console\Output;

class ListRoutesCommand extends Command
{
    protected static $defaultName = 'health:routes';
    protected $description = '列出所有 Webman 路由接口';

    public function handle(Input $input, Output $output)
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
        $output->writeln('<info>接口路由列表：</info>');
        $output->table(['Method', 'Path', 'Callback', 'Middleware'], $routes);
        return 0;
    }
}
