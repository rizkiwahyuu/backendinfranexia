<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DisturbanceController;
use App\Http\Controllers\Api\FieldReportController;
use App\Http\Controllers\Api\MetaController;
use App\Http\Controllers\Api\PruningTaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => ['status' => 'ok']);
Route::get('/meta', MetaController::class);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('users', UserController::class);
Route::apiResource('assets', AssetController::class);
Route::apiResource('disturbances', DisturbanceController::class);
Route::apiResource('pruning-tasks', PruningTaskController::class);
Route::apiResource('field-reports', FieldReportController::class);
Route::apiResource('activity-logs', ActivityLogController::class)->only(['index', 'show', 'store']);
