<?php

namespace Health\Bridge\Webman\Console;

use Health\Core\HealthOrchestrator;
use Health\Core\Config;
use support\Console\Command;
use support\Console\Input;
use support\Console\Output;

class HealthCheckCommand extends Command
{
    protected static $defaultName = 'health:check';
    protected $description = '健康检查：检测所有外部依赖并输出/记录结果';

    public function handle(Input $input, Output $output)
    {
        $configArr = config('plugin.health', []);
        $manager = new HealthOrchestrator(null, new Config($configArr));
        $results = $manager->checkAll(false);

        $down = [];
        foreach ($results as $result) {
            $status = $result->status ?? '';
            if ($status !== 'UP') {
                $down[] = $result;
            }
        }

        $rows = array_map(function ($r) {
            return [
                $r->service,
                $r->status,
                $r->latency,
                $r->message,
                $r->time,
            ];
        }, $results);

        $output->writeln('<info>健康检查结果：</info>');
        $output->table(['Service', 'Status', 'Latency(ms)', 'Message', 'Time'], $rows);

        if (!empty($down)) {
            $output->writeln('<error>发现异常服务，请检查告警配置！</error>');
            return 1;
        } else {
            $output->writeln('<info>所有服务健康</info>');
            return 0;
        }
    }
}
