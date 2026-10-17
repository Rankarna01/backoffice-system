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

// Learning Certificates Endpoints
Route::get('/certificates/verify/{certificate_number}', [CertificateController::class, 'verify'])->name('api.v1.certificates.verify');
Route::get('/certificates', [CertificateController::class, 'index'])->name('api.v1.certificates.index');
Route::get('/certificates/stats', [CertificateController::class, 'stats'])->name('api.v1.certificates.stats');
Route::post('/certificates/claim', [CertificateController::class, 'claim'])->name('api.v1.certificates.claim');
Route::get('/certificates/{certificate_number}', [CertificateController::class, 'show'])->name('api.v1.certificates.show');

// Market Signals Endpoints
Route::get('/signals', [\App\Http\Controllers\Api\V1\SignalController::class, 'index'])->name('api.v1.signals.index');
Route::get('/signals/stats', [\App\Http\Controllers\Api\V1\SignalController::class, 'stats'])->name('api.v1.signals.stats');
Route::get('/signals/{id}', [\App\Http\Controllers\Api\V1\SignalController::class, 'show'])->name('api.v1.signals.show');

// Market Outlooks Endpoints
Route::get('/market-outlooks', [\App\Http\Controllers\Api\V1\MarketOutlookController::class, 'index'])->name('api.v1.market-outlooks.index');
Route::get('/market-outlooks/stats', [\App\Http\Controllers\Api\V1\MarketOutlookController::class, 'stats'])->name('api.v1.market-outlooks.stats');
Route::get('/market-outlooks/{slug}', [\App\Http\Controllers\Api\V1\MarketOutlookController::class, 'show'])->name('api.v1.market-outlooks.show');

// Market News Endpoints
Route::get('/market-news', [\App\Http\Controllers\Api\V1\MarketNewsController::class, 'index'])->name('api.v1.market-news.index');
Route::get('/market-news/stats', [\App\Http\Controllers\Api\V1\MarketNewsController::class, 'stats'])->name('api.v1.market-news.stats');
Route::get('/market-news/{slug}', [\App\Http\Controllers\Api\V1\MarketNewsController::class, 'show'])->name('api.v1.market-news.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('api.v1.me');

    // Authenticated Student Learning Endpoints
    Route::get('/my-courses', [MyCourseController::class, 'index'])->name('api.v1.my-courses.index');
    Route::get('/courses/{slug}/classroom', [MyCourseController::class, 'classroom'])->name('api.v1.courses.classroom');
    Route::get('/lessons/{uuid}', [LessonProgressController::class, 'show'])->name('api.v1.lessons.show');
    Route::post('/lessons/{uuid}/progress', [LessonProgressController::class, 'updateProgress'])->name('api.v1.lessons.progress');
    Route::get('/quizzes/{id}', [QuizController::class, 'show'])->name('api.v1.quizzes.show');
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit'])->name('api.v1.quizzes.submit');
    
    // Commerce
    Route::get('/orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'index'])->name('api.v1.orders.index');
    Route::get('/orders/stats', [\App\Http\Controllers\Api\V1\OrderController::class, 'stats'])->name('api.v1.orders.stats');
    Route::post('/orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'store'])->name('api.v1.orders.store');
});

// Trading Journal (Manual & MT5) Endpoints
Route::get('/trading/journal', [\App\Http\Controllers\Api\V1\TradingJournalController::class, 'index'])->name('api.v1.trading.journal.index');
Route::get('/trading/journal/stats', [\App\Http\Controllers\Api\V1\TradingJournalController::class, 'stats'])->name('api.v1.trading.journal.stats');
Route::post('/trading/journal', [\App\Http\Controllers\Api\V1\TradingJournalController::class, 'store'])->name('api.v1.trading.journal.store');
Route::get('/trading/journal/{id}', [\App\Http\Controllers\Api\V1\TradingJournalController::class, 'show'])->name('api.v1.trading.journal.show');
Route::put('/trading/journal/{id}', [\App\Http\Controllers\Api\V1\TradingJournalController::class, 'update'])->name('api.v1.trading.journal.update');
Route::delete('/trading/journal/{id}', [\App\Http\Controllers\Api\V1\TradingJournalController::class, 'destroy'])->name('api.v1.trading.journal.destroy');

// Market Live Chart & TradingView Embed Config
Route::get('/market/chart/config', [\App\Http\Controllers\Api\V1\MarketChartConfigController::class, 'show'])->name('api.v1.market.chart.config');

// Live Paper Trading Simulation Endpoints (with auth fallback for demo testing)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/market/trading/account', [\App\Http\Controllers\Api\V1\TradingSimulatorController::class, 'getAccount'])->name('api.v1.market.trading.account');
    Route::get('/market/trading/positions', [\App\Http\Controllers\Api\V1\TradingSimulatorController::class, 'getPositions'])->name('api.v1.market.trading.positions');
    Route::post('/market/trading/order', [\App\Http\Controllers\Api\V1\TradingSimulatorController::class, 'openOrder'])->name('api.v1.market.trading.order');
    Route::post('/market/trading/positions/{id}/close', [\App\Http\Controllers\Api\V1\TradingSimulatorController::class, 'closePosition'])->name('api.v1.market.trading.positions.close');
    Route::put('/market/trading/positions/{id}/sltp', [\App\Http\Controllers\Api\V1\TradingSimulatorController::class, 'updateSlTp'])->name('api.v1.market.trading.positions.sltp');
    Route::post('/market/trading/account/reset', [\App\Http\Controllers\Api\V1\TradingSimulatorController::class, 'resetAccount'])->name('api.v1.market.trading.account.reset');
});

// Learning Bookmarks Endpoints
Route::get('/bookmarks', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'index'])->name('api.v1.bookmarks.index');
Route::get('/bookmarks/stats', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'stats'])->name('api.v1.bookmarks.stats');
Route::post('/bookmarks', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'store'])->name('api.v1.bookmarks.store');
Route::put('/bookmarks/{id}', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'update'])->name('api.v1.bookmarks.update');
Route::delete('/bookmarks/{id}', [\App\Http\Controllers\Api\V1\BookmarkController::class, 'destroy'])->name('api.v1.bookmarks.destroy');

// Community & Forum Discussion Endpoints
Route::get('/discussions', [\App\Http\Controllers\Api\V1\DiscussionController::class, 'index'])->name('api.v1.discussions.index');
Route::get('/discussions/categories', [\App\Http\Controllers\Api\V1\DiscussionController::class, 'categories'])->name('api.v1.discussions.categories');
Route::get('/discussions/{slug}', [\App\Http\Controllers\Api\V1\DiscussionController::class, 'show'])->name('api.v1.discussions.show');
Route::post('/discussions', [\App\Http\Controllers\Api\V1\DiscussionController::class, 'store'])->name('api.v1.discussions.store');
Route::post('/discussions/{id}/replies', [\App\Http\Controllers\Api\V1\DiscussionController::class, 'reply'])->name('api.v1.discussions.reply');
Route::post('/discussions/{id}/like', [\App\Http\Controllers\Api\V1\DiscussionController::class, 'like'])->name('api.v1.discussions.like');
Route::post('/discussions/replies/{id}/like', [\App\Http\Controllers\Api\V1\DiscussionController::class, 'likeReply'])->name('api.v1.discussions.replies.like');
Route::post('/discussions/{id}/solution/{replyId}', [\App\Http\Controllers\Api\V1\DiscussionController::class, 'markSolution'])->name('api.v1.discussions.solution');





