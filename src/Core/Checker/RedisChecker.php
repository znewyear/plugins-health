<?php

namespace Health\Core\Checker;

use Exception;
use Health\Core\Health;
use Health\Core\Inspection;
use Health\Core\HealthStatus;

/**
 * Redis 健康检查器
 *
 * 配置示例（config/health.php services 节点）：
 * [
 *     'check' => \Health\Core\Health::REDIS,
 *     'name' => 'redis',
 *     'host' => '127.0.0.1',
 *     'port' => 6379,
 *     'timeout' => 2,
 *     'driver' => 'phpredis' // 或 'predis'
 * ]
 */
class RedisChecker implements HealthCheckerInterface
{
    protected $client;

    /**
     * @param \Redis|\Predis\Client|null $client 可选，优先使用
     */
    public function __construct($client = null)
    {
        $this->client = $client;
    }

    public function check(Inspection $item, HealthStatus $status): bool
    {
        $start = microtime(true);

        try {
            $client = $this->getClient($item);
            $pong =  $client->ping();
            if (method_exists($client, 'close')) {
                $client->close();
            }
            if (gettype($pong) == 'boolean') {
                if (!$pong) {
                    throw new Exception('connection failed.');
                }
            } else {
                if ($pong !== '+PONG' && $pong !== 'PONG') {
                    throw new Exception('Unexpected PING response: ' . $pong);
                }
            }
        
           
            $status->setStatus(Health::STATUS_UP);
            $ok = true;
        } catch (Exception $e) {
            $status->setStatus(Health::STATUS_DOWN)
                ->setMessage($e->getMessage());
            $ok = false;
        }
        $latency = round((microtime(true) - $start) * 1000, 2);
        $status->setLatency($latency);

        return $ok;
    }

    public function getClient(Inspection $item)
    {
        $host = $item->get('host', '127.0.0.1');
        $port = $item->get('port', 6379);
        $timeout = $item->get('timeout', 2);
        $driver = $item->get('driver', 'phpredis');
        if ($this->client instanceof \Redis) {
            return $this->client;
        } elseif (class_exists('\Predis\Client') && ($this->client instanceof \Predis\Client || $driver === 'predis')) {
            $client = $this->client instanceof \Predis\Client
                ? $this->client
                : new \Predis\Client(['host' => $host, 'port' => $port, 'timeout' => $timeout]);
        } else {
            $client = new \Redis();
            $client->connect($host, $port, $timeout);
        }

        $this->client = $client;
        return $client;
    }
}
