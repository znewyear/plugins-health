<?php

namespace Health\Core;

use Health\Core\Inspection;

/**
 * 健康检查状态对象
 */
class HealthStatus
{
    /**
     * @var Inspection
     */
    protected $inspection;
    public $status;
    public $latency = 0;
    public $message = 'OK';
    public $time;
    public $checkMethod;
    public $raws = [];

    public function __construct(Inspection $inspection, $status = '', $latency = 0, $message = '', $time = null)
    {
        $this->inspection = $inspection;
        $this->status = $status;
        $this->latency = $latency;
        $this->message = $message;
        $this->time = $time ?: date('Y-m-d H:i:s');
    }

    /**
     * @return Inspection
     */
    public function inspection()
    {
        return $this->inspection;
    }

    public function getMethod($key, $default = null)
    {
        return $this->checkMethod;
    }

    public function setMethod($method)
    {
        $this->checkMethod = $method;
        return $this;
    }

    public function get($key, $default = null)
    {
        return $this->raws[$key] ?? $default;
    }

    public function set($key, $value)
    {
        $this->raws[$key] = $value;
        return $this;
    }

    public function raws()
    {
        return $this->raws;
    }

    public function setLatency($latency)
    {
        $this->latency = $latency;
        return $this;
    }
    
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function setMessage($msg)
    {
        $this->message = $msg;
        return $this;
    }

    public function isOk()
    {
        return $this->status === Health::STATUS_UP;
    }

    public function isTimeout()
    {
        return $this->status === 'TIMEOUT';
    }

    public function isError()
    {
        return $this->status === 'DOWN';
    }

    public function isDanger()
    {
        return $this->status === 'DANGER';
    }

    public function toArray(): array
    {
        return [
            'service' => $this->inspection->getName(),
            'status'  => $this->status,
            'latency' => $this->latency,
            'message' => $this->message,
            'time'    => $this->time,
        ];
    }
}
