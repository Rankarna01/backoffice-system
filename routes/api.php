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
Route::post('/register', [AuthController::class, 'register'])->name('api.v1.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.v1.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('api.v1.logout');

// Public Market & Economic Calendar Widget Config
Route::get('/customers/{id}/economic-calendar-config', [\App\Http\Controllers\Api\V1\EconomicCalendarConfigController::class, 'show'])
    ->name('api.v1.customers.economic-calendar-config');
Route::get('/economic-calendar-config', [\App\Http\Controllers\Api\V1\EconomicCalendarConfigController::class, 'show'])
    ->name('api.v1.economic-calendar-config');

use App\Http\Controllers\Api\V1\CertificateController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\LessonProgressController;
use App\Http\Controllers\Api\V1\MyCourseController;
use App\Http\Controllers\Api\V1\QuizController;

// Public Learning & Course Catalog Endpoints
Route::get('/courses', [CourseController::class, 'index'])->name('api.v1.courses.index');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('api.v1.courses.show');
Route::get('/certificates/verify/{certificate_number}', [CertificateController::class, 'verify'])->name('api.v1.certificates.verify');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('api.v1.me');

    // Authenticated Student Learning Endpoints
    Route::get('/my-courses', [MyCourseController::class, 'index'])->name('api.v1.my-courses.index');
    Route::get('/courses/{slug}/classroom', [MyCourseController::class, 'classroom'])->name('api.v1.courses.classroom');
    Route::get('/lessons/{uuid}', [LessonProgressController::class, 'show'])->name('api.v1.lessons.show');
    Route::post('/lessons/{uuid}/progress', [LessonProgressController::class, 'updateProgress'])->name('api.v1.lessons.progress');
    Route::get('/quizzes/{id}', [QuizController::class, 'show'])->name('api.v1.quizzes.show');
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit'])->name('api.v1.quizzes.submit');
});

