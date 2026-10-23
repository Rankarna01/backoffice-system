<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Redirect /login to Filament Admin Panel login
Route::redirect('/login', '/admin/login')->name('login');

// Direct /api route alias without v1 prefix
Route::prefix('api')->group(function () {
    Route::get('/customers/{id}/economic-calendar-config', [\App\Http\Controllers\Api\V1\EconomicCalendarConfigController::class, 'show']);
    Route::get('/economic-calendar-config', [\App\Http\Controllers\Api\V1\EconomicCalendarConfigController::class, 'show']);
});

// Official Digital Certificate Printable View (A4 Landscape)
Route::get('/certificates/{certificate_number}/view', [\App\Http\Controllers\CertificateWebController::class, 'view'])
    ->name('certificates.web.view');

