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
    ->withMiddleware(function (Middleware $middleware): void {
            $middleware->appendToGroup('web', [
        \App\Http\Middleware\TrustProxies::class,
    ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            '2fa' => \App\Http\Middleware\EnsureTwoFactorVerified::class,
        ]);
        
        $middleware->validateCsrfTokens(except: [
            'payment/webhook',
        ]);

        $middleware->appendToGroup('web', function (\Illuminate\Http\Request $request, \Closure $next) {
            if (config('app.env') === 'staging' && !$request->secure()) {
                return redirect()->secure($request->getRequestUri());
            }
            return $next($request);
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
