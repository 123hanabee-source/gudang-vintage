<?php

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * PETUNJUK: Tambahkan alias middleware berikut ke dalam
 * App\Http\Kernel  →  $middlewareAliases  (Laravel 10)
 * atau $routeMiddleware (Laravel 9 ke bawah).
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * Buka file:  app/Http/Kernel.php
 * Cari array: $middlewareAliases  (atau $routeMiddleware)
 * Tambahkan dua baris berikut:
 *
 *     'admin'  => \App\Http\Middleware\AdminMiddleware::class,
 *     'active' => \App\Http\Middleware\EnsureUserIsActive::class,
 *
 * Contoh lengkap setelah ditambahkan:
 */

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ... (middleware lain tidak diubah) ...

    protected $middlewareAliases = [
        // bawaan Laravel (jangan dihapus):
        'auth'             => \App\Http\Middleware\Authenticate::class,
        'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'     => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers'    => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'              => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive'     => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed'           => \App\Http\Middleware\ValidateSignature::class,
        'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // ✅ TAMBAHKAN DUA BARIS INI:
        'admin'  => \App\Http\Middleware\AdminMiddleware::class,
        'active' => \App\Http\Middleware\EnsureUserIsActive::class,
    ];
}
