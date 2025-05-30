# plugins-health 健康检查插件

## 项目简介

本项目为企业级 PHP 健康检查插件，支持 Lumen/Laravel/Laminas/Webman 等主流框架，核心无依赖、适配层解耦，支持 HTTP、MySQL、Redis、OSS/S3 等依赖检测、分级告警、事件通知、接口自动扫描、调用日志、命令行工具等功能。适用于微服务、SaaS、企业后台等多场景健康监控。

---

## 配置项说明

`config/health.php` 示例：

```php
return [
    'enabled' => true,
    'services' => [
        [
            'check' => \Health\Core\Health::MYSQL,
            'name' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'test',
            'username' => 'root',
            'password' => '',
            'require' => \Health\Core\Health::REQUIRE_CRITICAL, // 依赖级别
            'timeout' => 2,
            // 'event' => MyHealthEvent::class, // 可选，事件类名
        ],
        [
            'check' => \Health\Core\Health::REDIS,
            'name' => 'redis',
            'host' => '127.0.0.1',
            'port' => 6379,
            'require' => \Health\Core\Health::REQUIRE_REQUIRED,
            'timeout' => 2,
        ],
        [
            'check' => \Health\Core\Health::HTTP,
            'name' => 'external-api',
            'url' => 'https://api.example.com/health',
            'method' => 'GET',
            'timeout' => 3,
            'require' => \Health\Core\Health::REQUIRE_OPTIONAL,
        ],
        // ...
    ],
    'log' => [
        'table' => 'health_call_logs',
        'retention_days' => 30,
    ],
    // 全局事件（可选）
    // 'event' => MyHealthEvent::class,
];
```

### 依赖级别常量

- `Health::REQUIRE_CRITICAL`：系统核心依赖
- `Health::REQUIRE_REQUIRED`：业务主流程依赖（默认）
- `Health::REQUIRE_OPTIONAL`：可选依赖
- `Health::REQUIRE_EXTERNAL`：外部第三方依赖

### 事件机制

- 实现 `HealthEventInterface`，如：
  ```php
  class MyHealthEvent implements HealthEventInterface {
      public function success($status) { ... }
      public function wrong($status) { ... }
      public function error($status) { ... }
      public function danger($status) { ... }
  }
  ```
- 配置项可全局或单项指定 'event' => MyHealthEvent::class

---

## 核心实现逻辑

- **解耦架构**：核心逻辑（Orchestrator/Checker/Inspection/HealthStatus）无框架依赖，适配层负责注册、路由、命令等集成。
- **Checker**：每种依赖类型实现独立 Checker，支持 PDO/Redis/Predis/HTTP/OSS 等多种方式。
- **Orchestrator**：统一调度所有检查项，支持批量/单项检查、超时、分级、事件通知。
- **事件机制**：支持 success/wrong/error/danger 四种事件，自动分派，便于自定义告警、日志、通知等。
- **接口自动扫描**：各框架适配层均支持路由自动扫描，支持 controller/action 拆分、全局/路由中间件展示。
- **命令行工具**：各框架均有 health:check、health:routes 命令，便于运维和自动化。

---

## Lumen 项目集成与使用

### 1. 安装

- 在主项目 composer.json repositories 字段添加 vcs 仓库（如自建 GitLab）：
  ```json
  "repositories": [
    {
        "type": "git",
        "url": "https://git.ycgame.com/ycgame/General-Framework-Background-Operations/plug_health.git"
    }
  ]
  ```
- 安装依赖：
```shell
#开发模式
composer require ycgame/plugins-health:@dev --dev

#常规安装使用
composer require ycgame/plugins-health:1.0.1
```

### 2. 注册服务与配置

- 在 `bootstrap/app.php` 添加：
  ```php
  $app->register(Health\Bridge\Lumen\HealthServiceProvider::class);
  $app->configure('health');
  // 可选：注册日志中间件
  $app->middleware([
      Health\Bridge\Lumen\HealthLogMiddleware::class,
  ]);
  ```

### 3. 路由自动注册

- HealthServiceProvider 已自动注册 /health/status、/health/logs、/health/routes 路由，无需手动 require 路由文件。

### 4. 配置 health.php

- 编辑 `config/health.php`，按需添加服务项、依赖级别、事件等。

### 5. 运行/访问

- 访问 `http://localhost:8000/health/status` 查看健康检查结果。
- 访问 `http://localhost:8000/health/routes` 查看所有接口列表。
- 运行 `php artisan health:check` 检查所有依赖。
- 运行 `php artisan health:routes` 列出所有路由。

### 6. 日志与事件

- 检查日志自动落库到 `health_call_logs` 表（需执行 migration）。
- 事件机制支持全局和单项自定义，便于分级告警和业务扩展。

---

## 典型用例与扩展

- 支持多环境/多服务健康检查，适用于微服务、SaaS、企业后台等场景。
- 可扩展自定义 Checker、事件、告警、日志等。
- 支持多框架适配，核心逻辑完全解耦，便于维护和二次开发。

---

## 生产环境安装说明

- 推荐通过公司自建 GitLab/VCS 仓库集成，无需发布到 Packagist。
- 生产环境只需 require 必要依赖，开发/测试时可 require-dev 所有适配层依赖。
- 详细安装方式见上文。


