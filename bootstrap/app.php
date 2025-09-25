<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ PERBAIKAN: Pastikan web middleware group memiliki CSRF protection
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        
        // ✅ PERBAIKAN: Tambahkan CSRF protection secara eksplisit
        $middleware->validateCsrfTokens(except: [
            // Jika ada route yang perlu dikecualikan dari CSRF
        ]);
        
        // Daftarkan middleware alias
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'unit.access' => \App\Http\Middleware\UnitAccessMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();