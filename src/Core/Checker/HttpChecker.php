<?php

namespace Health\Core\Checker;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Health\Core\Health;
use Health\Core\Inspection;
use Health\Core\HealthStatus;

/**
 * HTTP 健康检查器
 *
 * 配置示例（config/health.php services 节点）：
 * [
 *     'check' => \Health\Core\Health::HTTP,
 *     'name' => 'external-api',
 *     'url' => 'https://api.example.com/health',
 *     'method' => 'GET',
 *     'headers' => [],
 *     'timeout' => 3,
 *     'auth' => [
 *         'type' => 'token',
 *         'key' => 'your-token'
 *     ]
 * ]
 */
class HttpChecker implements HealthCheckerInterface
{
    public function check(Inspection $item, HealthStatus $status): bool
    {
        $url = $item->get('url', '');
        $method = $item->get('method', 'GET');
        $headers = $item->get('headers', []);
        $timeout = $item->get('timeout', 3);
        $auth = $item->get('auth', null);
        $start = microtime(true);

        try {
            if ($auth && isset($auth['type']) && $auth['type'] === 'token' && isset($auth['key'])) {
                $headers['Authorization'] = 'Bearer ' . $auth['key'];
            }
            $client = new Client(['timeout' => $timeout]);
            $response = $client->request($method, $url, [
                'headers' => $headers,
            ]);
            $code = $response->getStatusCode();
            if ($code < 200 || $code >= 300) {
                $status->setStatus(Health::STATUS_DOWN)
                    ->setMessage("HTTP status $code");
                $ok = false;
            } else {
                $status->setStatus(Health::STATUS_UP);
                $ok = true;
            }
        } catch (RequestException $e) {
            $status->setStatus(Health::STATUS_DOWN)
                ->setMessage($e->getMessage());
            $ok = false;
        }
        $latency = round((microtime(true) - $start) * 1000, 2);
        $status->setLatency($latency);

        return $ok;
    }
}
