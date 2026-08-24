<?php

use App\Http\Controllers\MonitoringController;
use Illuminate\Support\Facades\Route;

// Monitoring
Route::get('/monitoring', [MonitoringController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:view-monitoring|manage-monitoring'])
    ->name('monitoring.index');