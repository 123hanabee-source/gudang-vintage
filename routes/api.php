<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicProductController;
use App\Http\Controllers\Api\ClientOrderController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use Illuminate\Support\Facades\Route;

// ── Auth (open — no middleware) ──────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/login',  [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
});

// ── Public storefront routes (open) ─────────────────────────────────────────
Route::get('/products',            [PublicProductController::class, 'index']);
Route::get('/products/{product}',  [PublicProductController::class, 'show']);
Route::get('/categories',          [PublicProductController::class, 'categories']);

// ── Customer orders ──────────────────────────────────────────────────────────
Route::post('/orders',             [ClientOrderController::class, 'store']);
Route::get('/customer/orders',     [ClientOrderController::class, 'history']);

// ── Admin routes ─────────────────────────────────────────────────────────────
// For production, wrap this group with: ->middleware('auth:sanctum')
Route::prefix('admin')->name('api.admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

    Route::apiResource('products', ProductController::class);
    Route::patch('products/{id}/restore', [ProductController::class, 'restore'])
         ->name('products.restore');

    Route::apiResource('customers', CustomerController::class);

    Route::apiResource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
         ->name('users.toggle-status');

    // ── Orders (read + mark paid) ──────────────────────────
    Route::get('orders',                              [AdminOrderController::class, 'index']);
    Route::patch('payments/{payment}/mark-paid',      [AdminOrderController::class, 'markPaid']);
});
