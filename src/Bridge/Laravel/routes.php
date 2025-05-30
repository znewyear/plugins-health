<?php

use Illuminate\Support\Facades\Route;

Route::prefix('health')->group(function () {
    Route::get('status', [\Health\Bridge\Laravel\HealthController::class, 'status']);
    Route::get('logs', [\Health\Bridge\Laravel\HealthController::class, 'logs']);
});
