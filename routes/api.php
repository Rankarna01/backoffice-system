<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - V1
|--------------------------------------------------------------------------
|
| Base path: /api/v1
| All endpoints follow the OpenAPI contract and modular domain architecture.
|
*/

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'TradingEdu API',
        'version' => 'v1',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Authentication endpoints
Route::post('/login', [AuthController::class, 'login'])->name('api.v1.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('api.v1.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.v1.logout');
});
