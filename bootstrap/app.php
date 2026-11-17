<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// Auto-cleanup stale package cache if it references removed BladeIconsServiceProvider
foreach (['packages.php', 'services.php'] as $cacheFile) {
    $cachePath = __DIR__ . '/cache/' . $cacheFile;
    if (file_exists($cachePath)) {
        $content = @file_get_contents($cachePath);
        if ($content && str_contains($content, 'BladeIconsServiceProvider')) {
            @unlink($cachePath);
        }
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->append(\App\Http\Middleware\RedirectIfUppercase::class);
        $middleware->append(\App\Http\Middleware\TrackSiteVisits::class);
        $middleware->validateCsrfTokens(except: [
            'payments/webhook',
            'telegram/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
