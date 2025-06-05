<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\TenantMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'tenant' => TenantMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/success',
            '/cancel',
            '/fail',
            '/orders/payment/success',
            '/orders/payment/cancel',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Add your exception handling customization here
    })
    ->create();
