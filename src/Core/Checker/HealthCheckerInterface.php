<?php

namespace Health\Core\Checker;
use Health\Core\Inspection;
use Health\Core\HealthStatus;

interface HealthCheckerInterface
{
    /**
     * 执行健康检查
     * @param Inspection $item 检查项配置
     * @param HealthStatus $status 检查结果对象
     * @return bool 检查是否通过
     */
    public function check(Inspection $item, HealthStatus $status): bool;
}
