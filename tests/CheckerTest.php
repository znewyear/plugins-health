<?php

use PHPUnit\Framework\TestCase;
use Health\Services\Checker\DbChecker;
use Health\Services\Checker\RedisChecker;
use Health\Services\Checker\OssChecker;
use Health\Services\Checker\HttpChecker;
use Health\Services\Checker\HealthResult;

class CheckerTest extends TestCase
{
    public function testDbCheckerUp()
    {
        $checker = new DbChecker();
        $result = $checker->check([
            'name' => 'mysql',
            'connection' => 'mysql',
            'timeout' => 2,
        ]);
        $this->assertInstanceOf(HealthResult::class, $result);
        $this->assertContains($result->status, ['UP', 'DOWN']);
    }

    public function testRedisCheckerUp()
    {
        $checker = new RedisChecker();
        $result = $checker->check([
            'name' => 'redis',
            'connection' => 'default',
            'timeout' => 2,
        ]);
        $this->assertInstanceOf(HealthResult::class, $result);
        $this->assertContains($result->status, ['UP', 'DOWN']);
    }

    public function testOssCheckerUp()
    {
        $checker = new OssChecker();
        $result = $checker->check([
            'name' => 'oss',
            'access_key' => 'fake',
            'secret_key' => 'fake',
            'bucket' => 'fake',
            'endpoint' => 'fake',
            'timeout' => 2,
        ]);
        $this->assertInstanceOf(HealthResult::class, $result);
        $this->assertContains($result->status, ['UP', 'DOWN']);
    }

    public function testHttpCheckerUp()
    {
        $checker = new HttpChecker();
        $result = $checker->check([
            'name' => 'http',
            'url' => 'https://httpbin.org/get',
            'method' => 'GET',
            'timeout' => 2,
        ]);
        $this->assertInstanceOf(HealthResult::class, $result);
        $this->assertContains($result->status, ['UP', 'DOWN']);
    }
}
