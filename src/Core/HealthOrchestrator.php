<?php

namespace Health\Core;

use ErrorException;
use Health\Core\Checker\HealthCheckerInterface;
use Health\Core\Checker\DbChecker;
use Health\Core\Checker\RedisChecker;
use Health\Core\Checker\OssChecker;
use Health\Core\Checker\HttpChecker;
use Health\Core\Health;
use Health\Core\Config;
use Health\Core\Inspection;
use Health\Core\HealthStatus;

class HealthOrchestrator
{
    protected $config;

    /**
     * @var \Closure
     */
    protected $factory;

    /**
     * Checker 类型映射
     * @var array
     */
    protected static $checkerMap = [
        Health::MYSQL => DbChecker::class,
        Health::REDIS => RedisChecker::class,
        Health::OSS => OssChecker::class,
        Health::HTTP => HttpChecker::class,
    ];

    protected $events = [
        Health::STATUS_UP      => 'success',
        Health::STATUS_TIMEOUT => 'wrong',
        Health::STATUS_DOWN    => 'error',
        Health::STATUS_DANGER  => 'danger',
    ];

    public function __construct(Config $config, $factory = null)
    {
        $this->config = $config;
        if ($factory) {
            $this->registerFactory($factory);
        }
    }

    public function registerFactory(\Closure $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    public function make($name, $params = null)
    {
        if ($this->factory instanceof \Closure) {
            return call_user_func_array($this->factory, [$name, $params]);
        } else {
            throw new ErrorException('not register factory');
        }
    }

    protected function getConfig($key, $default = null)
    {
        return $this->config->get($key, $default);
    }

    public function checkAll($batch = false)
    {
        $results = [];
        $services = $this->getConfig('services', []);

        foreach ($services as $service) {
            $inspection = new Inspection($service);
            $check = $inspection->getCheck();

            if ($check) {
                if (array_key_exists($check, self::$checkerMap)) {
                    $checkerClass = self::$checkerMap[$check];
                } else {
                    $checkerClass = $check;
                }

                if ($checkerClass) {
                    $results[] = $this->check(new Inspection($service), $checkerClass);
                }
            }
        }
        return $results;
    }

    /**
     * 检查单个服务配置
     * @param Inspection $inspection
     * @param string $check
     * @return HealthStatus
     */
    public function check(Inspection $inspection, $check)
    {
        $status = new HealthStatus($inspection);
        if ($check) {
            $checker = $this->make($check);

            if ($checker instanceof HealthCheckerInterface) {
                $ok = $checker->check($inspection, $status);
      
                // 事件通知（接口方式）
                $event = $inspection->get('event') ?: $this->config->get('event');
                if (is_string($event) && class_exists($event)) {
                    $event = $this->make($event);
                }
                if ($event && $event instanceof HealthEventInterface) {
                    $eventMethod = $this->getEventName($status->status);
                    if ($eventMethod) {
                        $event->{$eventMethod}($status);
                    }
                } else if ($event) {
                    throw new \ErrorException(
                        'Unrealized interface to HealthEventInterface By: ' . (is_object($event) ? get_class($event) : $event)
                    );
                }

                
                return $status;
            } else {
                throw new \ErrorException(
                    'not found checker ' . (is_object($check) ? get_class($check) : $check)
                );
            }
        }

      
        return $status->setStatus(Health::STATUS_UNKNOWN)
            ->setMessage('No checker found');
    }

    protected function getEventName(string $status)
    {
        return array_key_exists($status, $this->events) ? $this->events[$status] : null;
    }
}

