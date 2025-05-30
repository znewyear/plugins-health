<?php

use PHPUnit\Framework\TestCase;
use Health\Core\HealthOrchestrator;
use Health\Core\Config;
use Health\Core\HealthStatus;
use Health\Core\Inspection;

class HealthManagerTest extends TestCase
{
    public function testCheckAllRealtime()
    {
        $config = [
            'services' => [
                [
                    'check' => \Health\Core\Health::HTTP,
                    'name' => 'http',
                    'url' => 'https://httpbin.org/get',
                    'method' => 'GET',
                    'timeout' => 2,
                ],
            ],
        ];
        $orchestrator = new HealthOrchestrator(new Config($config), function($class) {
            return new $class();
        });
        $results = $orchestrator->checkAll(false);
        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
        $this->assertInstanceOf(HealthStatus::class, $results[0]);
        $this->assertContains($results[0]->status, ['UP', 'DOWN']);
    }
}
