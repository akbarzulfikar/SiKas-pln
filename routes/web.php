<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ReportController;

// ✅ TEST ROUTES - untuk debugging (hapus setelah selesai)
Route::get('/test', function () {
    return 'Hello World - App is working!';
});

Route::get('/test-db', function () {
    try {
        $pdo = DB::connection()->getPdo();
        return 'Database connection: SUCCESS';
    } catch (Exception $e) {
        return 'Database connection: FAILED - ' . $e->getMessage();
    }
});

Route::get('/test-env', function () {
    return response()->json([
        'DB_CONNECTION' => env('DB_CONNECTION'),
        'DB_HOST' => env('DB_HOST'),
        'DB_PORT' => env('DB_PORT'),
        'DB_DATABASE' => env('DB_DATABASE'),
        'DB_USERNAME' => env('DB_USERNAME'),
        'DATABASE_URL' => env('DATABASE_URL') ? 'SET' : 'NOT SET',
        'APP_KEY' => env('APP_KEY') ? 'SET' : 'NOT SET',
    ]);
});

// Redirect root to login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard.index');
    }
    return redirect()->route('login');
})->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard routes (accessible by all authenticated users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::post('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::post('/dashboard/category-data', [DashboardController::class, 'getCategoryData'])->name('dashboard.category-data');
    Route::get('/dashboard/realtime-stats', [DashboardController::class, 'getRealtimeStats'])->name('dashboard.realtime-stats');

    // Transaction routes (accessible by all authenticated users)
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/evidence', [TransactionController::class, 'showEvidence'])->name('show-evidence');

        // AJAX route untuk kategori
        Route::get('/categories-by-type', [TransactionController::class, 'getCategoriesByType'])->name('categories-by-type');
    });

    // Report routes (accessible by all authenticated users)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/categories-by-type', [ReportController::class, 'getCategoriesByType'])->name('categories-by-type');
        Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/print', [ReportController::class, 'printReport'])->name('print');
    });

    // Print routes (accessible by all authenticated users)
    Route::prefix('print')->name('print.')->group(function () {
        Route::get('/receipt/{id}', [PrintController::class, 'receipt'])->name('receipt');
        Route::get('/memo/{id}', [PrintController::class, 'memo'])->name('memo');

        // Stream routes (show PDF in browser)
        Route::get('/stream/receipt/{id}', [PrintController::class, 'streamReceipt'])->name('stream.receipt');
        Route::get('/stream/memo/{id}', [PrintController::class, 'streamMemo'])->name('stream.memo');

        // Preview routes for testing
        Route::get('/preview/receipt/{id}', [PrintController::class, 'previewReceipt'])->name('preview.receipt');
        Route::get('/preview/memo/{id}', [PrintController::class, 'previewMemo'])->name('preview.memo');
    });

    // ADMIN-ONLY ROUTES - Protected by middleware
    Route::middleware(['admin'])->group(function () {
        // Category Management Routes
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');

            // Special routes
            Route::post('/{id}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/export/csv', [CategoryController::class, 'export'])->name('export');
        });

        // User Management Routes
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');

            // Special routes
            Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{id}/change-password', [UserController::class, 'showChangePassword'])->name('change-password');
            Route::post('/{id}/change-password', [UserController::class, 'changePassword'])->name('change-password.update');
            Route::get('/export/csv', [UserController::class, 'export'])->name('export');
        });

        // Unit Management Routes
        Route::resource('units', UnitController::class)->parameters([
            'units' => 'unit'
        ]);

        // Special routes untuk Unit
        Route::post('units/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])
            ->name('units.toggle-status');
    });
});