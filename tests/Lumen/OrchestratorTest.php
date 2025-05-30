<?php

use Laravel\Lumen\Testing\TestCase;
use Health\Core\HealthOrchestrator;
use Health\Bridge\Lumen\HealthServiceProvider;

class LumenOrchestratorTest extends TestCase
{
    public function createApplication()
    {
        // 如无 bootstrap/app.php，可跳过测试
        $bootstrap = __DIR__ . '/../../bootstrap/app.php';
        if (!file_exists($bootstrap)) {
            $this->markTestSkipped('需在 Lumen 项目中集成测试');
        }
        $app = require $bootstrap;
        $app->register(HealthServiceProvider::class);
        $app->configure('health');
        return $app;
    }

    public function testHealthOrchestratorCheckAll()
    {
        if (!method_exists($this, 'app') || !$this->app) {
            $this->markTestSkipped('需在 Lumen 项目中集成测试');
        }
        $orchestrator = $this->app->make(HealthOrchestrator::class);
        $results = $orchestrator->checkAll();
        $this->assertIsArray($results);
        $this->assertNotEmpty($results);
        $this->assertObjectHasProperty('status', $results[0]);
        $this->assertContains($results[0]->status, ['UP', 'DOWN']);
    }
}
