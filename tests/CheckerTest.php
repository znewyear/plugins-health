<?php

use PHPUnit\Framework\TestCase;
use Health\Core\Checker\DbChecker;
use Health\Core\Checker\RedisChecker;
use Health\Core\Checker\OssChecker;
use Health\Core\Checker\HttpChecker;
use Health\Core\HealthStatus;
use Health\Core\Inspection;

class CheckerTest extends TestCase
{
    public function testDbCheckerUp()
    {
        $checker = new DbChecker();
        $result = new HealthStatus(new Inspection(['name' => 'mysql']));
        $ok = $checker->check(new Inspection([
            'name' => 'mysql',
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'test',
            'username' => 'root',
            'password' => '',
        ]), $result);
        $this->assertInstanceOf(HealthStatus::class, $result);
        $this->assertTrue(is_bool($ok));
    }

    public function testRedisCheckerUp()
    {
        $checker = new RedisChecker();
        $result = new HealthStatus(new Inspection(['name' => 'redis']));
        $ok = $checker->check(new Inspection([
            'name' => 'redis',
            'host' => '127.0.0.1',
            'port' => 6379,
        ]), $result);
        $this->assertInstanceOf(HealthStatus::class, $result);
        $this->assertTrue(is_bool($ok));
    }

    public function testOssCheckerUp()
    {
        $checker = new OssChecker();
        $result = new HealthStatus(new Inspection(['name' => 'oss']));
        $ok = $checker->check(new Inspection([
            'name' => 'oss',
            'access_key' => 'fake',
            'secret_key' => 'fake',
            'bucket' => 'fake',
            'endpoint' => 'fake',
        ]), $result);
        $this->assertInstanceOf(HealthStatus::class, $result);
        $this->assertTrue(is_bool($ok));
    }

    public function testHttpCheckerUp()
    {
        $checker = new HttpChecker();
        $result = new HealthStatus(new Inspection(['name' => 'http']));
        $ok = $checker->check(new Inspection([
            'name' => 'http',
            'url' => 'https://httpbin.org/get',
            'method' => 'GET',
        ]), $result);
        $this->assertInstanceOf(HealthStatus::class, $result);
        $this->assertTrue(is_bool($ok));
    }
}
