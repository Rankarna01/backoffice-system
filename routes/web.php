<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Direct /api route alias without v1 prefix
Route::prefix('api')->group(function () {
    Route::get('/customers/{id}/economic-calendar-config', [\App\Http\Controllers\Api\V1\EconomicCalendarConfigController::class, 'show']);
    Route::get('/economic-calendar-config', [\App\Http\Controllers\Api\V1\EconomicCalendarConfigController::class, 'show']);
});

