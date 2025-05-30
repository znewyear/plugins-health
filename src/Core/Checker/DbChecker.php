<?php

namespace Health\Core\Checker;

use Health\Core\Health;
use Health\Core\Inspection;
use Health\Core\HealthStatus;

/**
 * 数据库健康检查器
 *
 * 配置示例（config/health.php services 节点）：
 * [
 *     'check' => \Health\Core\Health::MYSQL,
 *     'name' => 'mysql',
 *     'host' => '127.0.0.1',
 *     'port' => 3306,
 *     'database' => 'test',
 *     'username' => 'root',
 *     'password' => '',
 *     'driver' => 'mysql', // 可选
 *     'timeout' => 2
 * ]
 */
class DbChecker implements HealthCheckerInterface
{
    protected $pdo;

    /**
     * @param \PDO|null $pdo 可选，优先使用
     */
    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function check(Inspection $item, HealthStatus $status): bool
    {
        $timeout = $item->get('timeout', 2);
        $start = microtime(true);

        try {
            if ($this->pdo instanceof \PDO) {
                $this->pdo->setAttribute(\PDO::ATTR_TIMEOUT, $timeout);
                $this->pdo->query('SELECT 1');
            } else {
                $dsn = $item->get('dsn');
                if (!$dsn) {
                    $driver = $item->get('driver', 'mysql');
                    $host = $item->get('host', '127.0.0.1');
                    $port = $item->get('port', 3306);
                    $database = $item->get('database', '');
                    if ($driver === 'mysql') {
                        $dsn = "mysql:host={$host};port={$port};dbname={$database}";
                    } elseif ($driver === 'pgsql') {
                        $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
                    } elseif ($driver === 'sqlsrv') {
                        $dsn = "sqlsrv:Server={$host},{$port};Database={$database}";
                    } elseif ($driver === 'sqlite') {
                        $dsn = "sqlite:{$database}";
                    } else {
                        throw new \Exception("Unsupported DB driver: $driver");
                    }
                }
                $username = $item->get('username', '');
                $password = $item->get('password', '');
                $pdo = new \PDO(
                    $dsn,
                    $username,
                    $password,
                    [\PDO::ATTR_TIMEOUT => $timeout]
                );
                $pdo->query('SELECT 1');
            }
            $status->setStatus(Health::STATUS_UP);
            $ok = true;
        } catch (\Exception $e) {
            $status->setStatus(Health::STATUS_DOWN)
                ->setMessage($e->getMessage());
            $ok = false;
        }
        $latency = round((microtime(true) - $start) * 1000, 2);
        $status->setLatency($latency);

        return $ok;
    }
}
