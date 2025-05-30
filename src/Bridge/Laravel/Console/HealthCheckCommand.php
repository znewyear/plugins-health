<?php

namespace Health\Bridge\Laravel\Console;

use Health\Core\Health;
use Illuminate\Console\Command;
use Health\Core\HealthOrchestrator;

class HealthCheckCommand extends Command
{
    protected $signature = 'health:check {--report : 批量入库模式}';
    protected $description = '健康检查：检测所有外部依赖并输出/记录结果';

    public function handle(HealthOrchestrator $orchestrator)
    {
        $batch = $this->option('report') ? true : false;
        $results = $orchestrator->checkAll($batch);

        $down = [];
        foreach ($results as $result) {
            $status = $result->status ?? '';
            if ($status !== Health::STATUS_UP) {
                $down[] = $result;
            }
        }

        $this->table(
            ['Service', 'Status', 'Latency(ms)', 'Message', 'Time'],
            array_map(function ($r) {
                return [
                    $r->inspection()->getName(),
                    $r->status,
                    $r->latency,
                    $r->message,
                    $r->time,
                ];
            }, $results)
        );

        if (!empty($down)) {
            $this->error('发现异常服务，请检查告警配置！');
        } else {
            $this->info('所有服务健康');
        }
    }
}
