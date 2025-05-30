<?php

use Orchestra\Testbench\TestCase;
use Health\Core\HealthOrchestrator;
use Health\Core\Config;

class OrchestratorTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Health\Bridge\Laravel\HealthServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('health', [
            'enabled' => true,
            'services' => [
                [
                    'check' => \Health\Core\Health::HTTP,
                    'name' => 'http',
                    'url' => 'https://httpbin.org/get',
                    'method' => 'GET',
                    'timeout' => 2,
                ],
            ],
        ]);
    }

    public function testHealthOrchestratorCheckAll()
    {
        $orchestrator = $this->app->make(HealthOrchestrator::class);
        $results = $orchestrator->checkAll();
        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
        $this->assertObjectHasAttribute('status', $results[0]);
        $this->assertContains($results[0]->status, ['UP', 'DOWN']);
    }
}
