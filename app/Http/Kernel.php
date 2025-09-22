<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     * In Laravel 11, most middleware is configured in bootstrap/app.php
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Global middleware can be added here if needed
        // Most middleware is now configured in bootstrap/app.php
    ];

    /**
     * The application's route middleware groups.
     *
     * In Laravel 11, middleware groups are typically configured in bootstrap/app.php
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // Web middleware group is configured in bootstrap/app.php
        ],

        'api' => [
            // API middleware group can be configured here if needed
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * In Laravel 11, middleware aliases are registered in bootstrap/app.php
     * This array is kept for backward compatibility but should remain minimal.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        // In Laravel 11, middleware aliases are registered in bootstrap/app.php
        // This array is kept minimal for backward compatibility
        
        // Only include core Laravel middleware that definitely exist
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        
        // Custom middleware aliases are registered in bootstrap/app.php:
        // 'admin' => \App\Http\Middleware\AdminMiddleware::class,
        // 'unit.access' => \App\Http\Middleware\UnitAccessMiddleware::class,
    ];
}