<?php

namespace Health\Bridge\Laminas\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Laminas\Router\RouteStackInterface;

class ListRoutesCommand extends Command
{
    protected static $defaultName = 'health:routes';
    protected static $defaultDescription = '列出所有 Laminas 路由接口';

    protected $router;

    public function __construct(RouteStackInterface $router)
    {
        parent::__construct();
        $this->router = $router;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $routes = [];
        foreach ($this->router->getRoutes() as $name => $route) {
            $routes[] = [
                'name' => $name,
                'type' => get_class($route),
                'route' => method_exists($route, 'getRoute') ? $route->getRoute() : '',
                'defaults' => json_encode(method_exists($route, 'getDefaults') ? $route->getDefaults() : []),
            ];
        }

        $output->writeln('<info>接口路由列表：</info>');
        $output->table(['Name', 'Type', 'Route', 'Defaults'], $routes);

        return Command::SUCCESS;
    }
}
