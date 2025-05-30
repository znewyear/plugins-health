# Lumen 健康检查模块

## 功能概述

- 实时/定时检测 HTTP API、MySQL、Redis、OSS/S3 等外部依赖
- 通信权限校验（Key/Token 有效性、访问权限）
- 请求调用日志（URI、参数、状态码、耗时、用户、IP）落库并支持检索导出
- 路由扫描列表，自动列出所有需监控 API
- CLI 命令与定时任务支持，异常时邮件/Slack/Webhook 告警
- 配置文件、ServiceProvider、数据库迁移、命令、Controller、路由、中间件、Blade 管理端页面、单元与集成测试

## 安装

1. **通过 Composer 安装依赖**

   ```bash
   composer require guhealth/lumen-health
   -- 开发模式安装
   composer require ycgame/plugins-health:@dev --dev --prefer-stable
   ```

2. **发布配置和迁移**

   ```bash
   php artisan vendor:publish --provider="App\Providers\HealthServiceProvider" --tag=config
   php artisan migrate
   ```

3. **注册 ServiceProvider**

   在 `bootstrap/app.php` 添加：

   ```php
   $app->register(App\Providers\HealthServiceProvider::class);
   ```

4. **中间件注册**

   在 `bootstrap/app.php` 注册调用日志中间件：

   ```php
   $app->middleware([
       App\Http\Middleware\HealthLogMiddleware::class,
   ]);
   ```

## 配置说明

编辑 `config/health.php`，示例：

```php
return [
    'enabled' => true,
    'services' => [
        [
            'type' => 'db',
            'name' => 'mysql',
            'connection' => 'mysql',
            'timeout' => 2,
        ],
        [
            'type' => 'redis',
            'name' => 'redis',
            'connection' => 'default',
            'timeout' => 2,
        ],
        [
            'type' => 'oss',
            'name' => 'oss',
            'access_key' => env('OSS_ACCESS_KEY'),
            'secret_key' => env('OSS_SECRET_KEY'),
            'bucket' => env('OSS_BUCKET'),
            'endpoint' => env('OSS_ENDPOINT'),
            'timeout' => 3,
        ],
        [
            'type' => 'http',
            'name' => 'external-api',
            'url' => 'https://api.example.com/health',
            'method' => 'GET',
            'headers' => [],
            'timeout' => 3,
            'auth' => [
                'type' => 'token',
                'key' => env('API_TOKEN'),
            ],
        ],
    ],
    'routes' => [
        'include' => [],
        'exclude' => [],
    ],
    'log' => [
        'table' => 'health_call_logs',
        'retention_days' => 30,
    ],
    'alert' => [
        'mail' => [
            'enabled' => false,
            'to' => env('HEALTH_ALERT_MAIL_TO'),
        ],
        'slack' => [
            'enabled' => false,
            'webhook_url' => env('HEALTH_ALERT_SLACK_WEBHOOK'),
        ],
        'webhook' => [
            'enabled' => false,
            'url' => env('HEALTH_ALERT_WEBHOOK_URL'),
        ],
        'threshold' => [
            'down' => 1,
            'timeout' => 3,
        ],
    ],
];
```

## 路由与接口

- `GET /health/status`：获取所有服务健康状态
- `GET /health/logs`：调用日志分页查询，支持 service/status/时间筛选

## 管理端页面

- 访问 `resources/views/health.blade.php`，或集成到自定义后台
- 实时健康状态面板、接口列表、日志筛选与导出

## CLI 命令与定时任务

- 手动检测：`php artisan health:check`
- 批量入库：`php artisan health:check --report`
- 可加入 crontab 定时执行，异常时自动告警

## 告警配置

- 支持邮件、Slack、Webhook，详见 `config/health.php` 的 `alert` 配置
- 实现告警逻辑请补充 `HealthCheckCommand` 中的 TODO 部分

## 单元与集成测试

- 运行所有测试：

  ```bash
  ./vendor/bin/phpunit
  ```

- 测试覆盖 Checker、Manager、Controller、命令、Middleware

## 常见 Q&A

- **Q: 如何扩展新的健康检查类型？**  
  A: 实现 `HealthCheckerInterface` 并在配置中添加 type。

- **Q: 如何自定义日志表结构？**  
  A: 修改迁移文件和 `config/health.php` 的 `log.table`。

- **Q: 如何集成到现有 RBAC？**  
  A: 日志中已记录 user 字段，前端可结合现有权限系统。

- **Q: 如何导出日志？**  
  A: 管理端页面支持一键导出 CSV。

## 目录结构

```
config/health.php
app/Providers/HealthServiceProvider.php
app/Services/HealthManager.php
app/Services/Checker/
app/Http/Controllers/HealthController.php
app/Http/Middleware/HealthLogMiddleware.php
app/Console/Commands/HealthCheckCommand.php
routes/health.php
database/migrations/xxxx_xx_xx_create_health_call_logs_table.php
resources/views/health.blade.php
tests/
README.md
```

## 依赖

- Lumen 8.x+
- guzzlehttp/guzzle
- 可选：aws/aws-sdk-php 或 aliyuncs/oss-sdk-php

## 贡献

欢迎 issue 和 PR！