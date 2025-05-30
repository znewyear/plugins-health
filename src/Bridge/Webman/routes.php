<?php

use Webman\Route;
use Health\Bridge\Webman\HealthPlugin;

// Webman 路由注册示例
Route::get('/plugin/health/status', [HealthPlugin::class, 'status']);
