<?php

namespace Health\Core;

use Health\Core\Inspection;
use Health\Core\HealthStatus;

/**
 * 健康检查事件接口，使用者可实现并注入
 */
interface HealthEventInterface
{
    /**
     * 检查成功（如 status=UP）
     */
    public function success(HealthStatus $status);

    /**
     * 检查超时（如 status=TIMEOUT）
     */
    public function wrong(HealthStatus $status);

    /**
     * 检查异常（如 status=DOWN）
     */
    public function error(HealthStatus $status);

    /**
     * 检查严重异常（如 status=DANGER/自定义）
     */
    public function danger(HealthStatus $status);
}
