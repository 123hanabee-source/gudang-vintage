<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Guest routes (tidak perlu login) ────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
         ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
         ->name('login.store');
});

// ── Authenticated routes ─────────────────────────────────
// Semua route di bawah butuh: sudah login + akun Aktif
Route::middleware(['auth', 'active'])->group(function () {

    // Redirect root → dashboard
    Route::get('/', fn () => redirect()->route('admin.dashboard'));

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
         ->name('logout');

    // ── Profile (semua role bisa edit profil sendiri) ──
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',               [ProfileController::class, 'edit'])           ->name('edit');
        Route::patch('/',             [ProfileController::class, 'update'])         ->name('update');
        Route::patch('/password',     [ProfileController::class, 'updatePassword']) ->name('password');
        Route::delete('/',            [ProfileController::class, 'destroy'])        ->name('destroy');
    });

    // ── Admin area ──────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
             ->name('dashboard');

        // ── Products (resource CRUD) ─────────────────
        Route::resource('products', ProductController::class);

        // Extra product routes (tidak tercover resource)
        Route::patch('products/{product}/stock',   [ProductController::class, 'updateStock'])  ->name('products.stock');
        Route::patch('products/{id}/restore',      [ProductController::class, 'restore'])      ->name('products.restore');
        Route::delete('products/{id}/force-delete',[ProductController::class, 'forceDestroy']) ->name('products.force-delete');

        // ── Users (Admin only, sudah terlindungi middleware admin) ─
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
             ->name('users.toggle-status');

        // ── Customers ────────────────────────────────
        Route::resource('customers', CustomerController::class);
    });
});
