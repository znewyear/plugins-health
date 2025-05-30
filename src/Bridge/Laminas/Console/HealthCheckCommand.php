<?php

namespace Health\Bridge\Laminas\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Health\Core\HealthOrchestrator;

class HealthCheckCommand extends Command
{
    protected static $defaultName = 'health:check';
    protected static $defaultDescription = '健康检查：检测所有外部依赖并输出/记录结果';

    protected $orchestrator;

    public function __construct(HealthOrchestrator $orchestrator)
    {
        parent::__construct();
        $this->orchestrator = $orchestrator;
    }

    protected function configure()
    {
        $this->addOption('report', null, InputOption::VALUE_NONE, '批量入库模式');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batch = $input->getOption('report') ? true : false;
        $results = $this->orchestrator->checkAll($batch);

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
            return Command::FAILURE;
        } else {
            $output->writeln('<info>所有服务健康</info>');
            return Command::SUCCESS;
        }
    }
}
