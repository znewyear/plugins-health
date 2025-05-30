<?php

use PHPUnit\Framework\TestCase;
use Health\Services\HealthManager;
use Health\Http\Controllers\HealthController;
use Illuminate\Http\Request;

class HealthControllerTest extends TestCase
{
    public function testStatusApi()
    {
        $this->markTestSkipped('HealthControllerTest 依赖 Lumen Controller，独立包环境下跳过。');
    }
}
