<?php

namespace Health\Core;

class Health
{
    const REDIS = 'redis';
    const MYSQL = 'mysql';
    const OSS = 'oss';
    const HTTP = 'http';

    const STATUS_UP = 'UP';
    const STATUS_DOWN = 'DOWN';
    const STATUS_TIMEOUT = 'TIMEOUT';
    const STATUS_UNKNOWN = 'UNKNOWN';
    const STATUS_DANGER = 'DANGER';

    // 依赖级别常量
    /**
     * 系统核心依赖，出错需高优先级告警
     */
    const REQUIRE_CRITICAL = 'critical';
    /**
     * 业务主流程依赖，出错需业务告警
     */
    const REQUIRE_REQUIRED = 'required';
    /**
     * 可选依赖，出错可降级或忽略
     */
    const REQUIRE_OPTIONAL = 'optional';
    /**
     * 外部第三方依赖，出错可自定义处理
     */
    const REQUIRE_EXTERNAL = 'external';
}
