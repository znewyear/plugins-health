<?php

use PHPUnit\Framework\TestCase;
use Health\Services\HealthManager;
use Health\Services\Checker\HealthResult;

class HealthManagerTest extends TestCase
{
    public function testCheckAllRealtime()
    {
        $config = [
            'services' => [
                [
                    'check' => \Health\Services\Health::HTTP,
                    'name' => 'http',
                    'url' => 'https://httpbin.org/get',
                    'method' => 'GET',
                    'timeout' => 2,
                ],
            ],
            'log' => [
                'table' => 'health_call_logs',
            ],
        ];
        $manager = new HealthManager(null, $config);
        $results = $manager->checkAll(false);
        $this->assertIsArray($results);
        $this->assertArrayHasKey('status', $results[0]);
        $this->assertContains($results[0]['status'], ['UP', 'DOWN']);
    }

    // batch模式需依赖 DB Facade，独立包测试环境下跳过
}
